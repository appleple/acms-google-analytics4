<?php

namespace Acms\Plugins\GoogleAnalytics4\Services;

require_once dirname(__FILE__).'/../vendor/autoload.php';

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\FilterExpressionList;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;

use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\Protobuf\Internal\RepeatedField;

/**
 * Analytics Data APIのサービス
 * @see https://developers.google.com/analytics/devguides/reporting/data/v1
 */
class AnalyticsData
{
    private $request = [];

    /**
     * Property IDをセットする
     *
     * @param string $propertyId
     * @return void
     */
    public function setPropertyId(string $propertyId)
    {
        $this->request['property'] = 'properties/' . $propertyId;
    }

    /**
     * 表示件数をセットする
     *
     * @param int $limit
     * @return void
     */
    public function setLimit(int $limit)
    {
        $this->request['limit'] = $limit;
    }

    /**
     * 集計期間をセットする
     *
     * @param string $start
     * @param string $end
     * @return void
     */
    public function setDateRange(string $start, string $end)
    {
        $this->request['dateRanges'][] = new DateRange([
            'start_date' => $start,
            'end_date' => $end,
        ]);
    }

    /**
     * ディメンションをセットする
     *
     * @param string $name
     * @return void
     */
    public function setDimension(string $name)
    {
        $this->request['dimensions'][] = new Dimension([
            'name' => $name
        ]);
    }

    /**
     * メトリクスをセットする
     *
     * @param string $name
     * @return void
     */
    public function setMetric(string $name)
    {
        $this->request['metrics'][] = new Metric([
            'name' => $name
        ]);
    }

    /**
     * Dimension Filterをセットする
     *
     * @param array $data
     * @return void
     */
    public function setDimensionFilter(array $data)
    {
        $this->request['dimensionFilter'] = new FilterExpression([
            $data['type'] => new FilterExpressionList([
                'expressions' => array_map(
                    function ($filter) {
                        return new FilterExpression([
                            'filter' => new Filter([
                                'field_name' => $filter['fieldName'],
                                'string_filter' => new StringFilter([
                                    'match_type' => MatchType::value($filter['matchType']),
                                    'value' => $filter['value'],
                                    'case_sensitive' => $filter['caseSensitive']
                                ])
                            ])
                        ]);
                    },
                    $data['filter']
                )
            ])
        ]);
    }

    /**
     * レポートを作成する
     *
     * @return RunReportResponse
     */
    protected function createReport()
    {
        $client = new BetaAnalyticsDataClient();
        return $client->runReport($this->request);
    }

    /**
     * レポートのRowsを取得する
     *
     * @return RepeatedField
     */
    public function getReportRows()
    {
        return $this->createReport()->getRows();
    }
}
