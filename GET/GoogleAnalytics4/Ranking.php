<?php

namespace Acms\Plugins\GoogleAnalytics4\GET\GoogleAnalytics4;

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
            'limit'      => intval(config('google-analytics4_ranking_limit')) ?: 30,
            'startDate'  => config('google-analytics4_ranking_start_date', '7daysAgo'),
            'endDate'    => config('google-analytics4_ranking_end_date', 'today'),
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
        $rows = $service->createReport()->getRows();

        if (count($rows) === 0) {
            return $Tpl->render([
                'notFound' => (object)[]
            ]);
        }

        return $Tpl->render([
            'ranking' => $this->buildRanking($rows)
        ]);
    }

    protected function buildRanking($rows)
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
}
