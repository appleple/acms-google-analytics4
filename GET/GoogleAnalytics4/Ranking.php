<?php

namespace Acms\Plugins\GoogleAnalytics4\GET\GoogleAnalytics4;

use Google\Protobuf\Internal\RepeatedField;

use Acms\Plugins\GoogleAnalytics4\Services\AnalyticsData;

use ACMS_GET;
use ACMS_Corrector;
use Template;

/**
 *  Analytics Data APIと連携して、
 *  Google Analytics4のアクセス数に基づいたランキングを表示するモジュール
 */
class Ranking extends ACMS_GET
{
    protected $config;

    /**
     * コンフィグの取得
     *
     * @return array
     */
    protected function initVars()
    {
        return [
            'propertyId' => config('google-analytics4_property_id'),
            'limit' => intval(config('google-analytics4_ranking_limit')) ?: 30,
            'startDate' => config('google-analytics4_ranking_start_date', '7daysAgo'),
            'endDate' => config('google-analytics4_ranking_end_date', 'today'),
            'filterType' => config('google-analytics4_ranking_filter_type', 'and_group'),
            'fieldNameAry' => configArray('google-analytics4_ranking_dimension_filters_field_name'),
            'matchTypeAry' => configArray('google-analytics4_ranking_dimension_filters_match_type'),
            'valueAry' => configArray('google-analytics4_ranking_dimension_filters_value'),
            'caseSensitiveAry' => configArray('google-analytics4_ranking_dimension_filters_case_sensitive')
        ];
    }

    /**
     * コンフィグのセット
     *
     * @return bool
     */
    protected function setConfig()
    {
        $this->config = $this->initVars();
        if ($this->config === false) {
            return false;
        }
        return true;
    }

    public function get()
    {
        if (!$this->setConfig()) {
            return '';
        }

        $Tpl = new Template($this->tpl, new ACMS_Corrector());
        $this->buildModuleField($Tpl);

        $service = new AnalyticsData();
        $service->setPropertyId($this->config['propertyId']);
        $service->setLimit($this->config['limit']);
        $service->setDateRange($this->config['startDate'], $this->config['endDate']);
        $service->setDimension('pageTitle');
        $service->setDimension('pagePath');
        $service->setMetric('screenPageViews');

        if (!empty($this->config['fieldNameAry'])) {
            $service->setDimensionFilter($this->createFilterData());
        }

        try {
            $rows = $service->getReportRows();
        } catch (\Google\ApiCore\ApiException $e) {
            userErrorLog('ACMS Error: In GoogleAnalytics4 extension -> ' . $e->getMessage());
            return $Tpl->render([
                'error' => (object)[]
            ]);
        }

        if ($rows->count() === 0) {
            return $Tpl->render([
                'notFound' => (object)[]
            ]);
        }

        return $Tpl->render([
            'ranking' => $this->buildRanking($rows)
        ]);
    }

    /**
     * ランキングの組み立て
     *
     * @param RepeatedField $rows
     * @return bool
     */
    protected function buildRanking(RepeatedField $rows)
    {
        $ranking = [];

        foreach ($rows as $row) {
            $ranking[] = [
                'title' => $row->getDimensionValues()[0]->getValue(),
                'path'  => $row->getDimensionValues()[1]->getValue(),
                'views' => $row->getMetricValues()[0]->getValue(),
            ];
        }

        return $ranking;
    }

    /**
     * フィルターデータの作成
     *
     * @return array
     */
    protected function createFilterData()
    {
        return [
            'type' => $this->config['filterType'],
            'filter' => array_map(
                function ($fieldName, $matchType, $value, $caseSensitive) {
                    return [
                        'fieldName' => $fieldName,
                        'matchType' => $matchType,
                        'value' => $value,
                        'caseSensitive' => !!($caseSensitive === 'on')
                    ];
                },
                $this->config['fieldNameAry'],
                $this->config['matchTypeAry'],
                $this->config['valueAry'],
                $this->config['caseSensitiveAry'],
            )
        ];
    }
}
