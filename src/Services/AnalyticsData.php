<?php

namespace Acms\Plugins\GoogleAnalytics4\Services;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\FilterExpressionList;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Protobuf\RepeatedField;

/**
 * Analytics Data APIのサービス
 * @see https://developers.google.com/analytics/devguides/reporting/data/v1
 */
class AnalyticsData
{
    private RunReportRequest $request;


    public function __construct()
    {
        $this->request = new RunReportRequest();
    }

    /**
     * Property IDをセットする
     *
     * @param string $propertyId
     * @return void
     */
    public function setPropertyId(string $propertyId)
    {
        $this->request->setProperty('properties/' . $propertyId);
    }

    /**
     * 表示件数をセットする
     *
     * @param int $limit
     * @return void
     */
    public function setLimit(int $limit)
    {
        $this->request->setLimit($limit);
    }

    /**
     * 集計期間をセットする
     *
     * @param array{start_date: string, end_date: string}[] $dateRanges
     * @return void
     */
    public function setDateRanges(array $dateRanges = [])
    {

        $dateRanges = array_map(function ($dateRange) {
            return new DateRange([
                'start_date' => $dateRange['start_date'],
                'end_date' => $dateRange['end_date'],
            ]);
        }, $dateRanges);
        $this->request->setDateRanges($dateRanges);
    }

    /**
     * ディメンションをセットする
     *
     * @param string[] $names
     * @return void
     */
    public function setDimensions(array $names)
    {
        $dimensions = array_map(function ($name) {
            return new Dimension([
                'name' => $name
            ]);
        }, $names);
        $this->request->setDimensions($dimensions);
    }

    /**
     * メトリクスをセットする
     *
     * @param string[] $names
     * @return void
     */
    public function setMetrics(array $names)
    {
        $metrics = array_map(function ($name) {
            return new Metric([
                'name' => $name
            ]);
        }, $names);
        $this->request->setMetrics($metrics);
    }

    /**
     * Dimension Filterをセットする
     *
     * @param array $data
     * @return void
     */
    public function setDimensionFilter(array $data)
    {
        $dimensionFilter = new FilterExpression([
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
        $this->request->setDimensionFilter($dimensionFilter);
    }

    /**
     * レポートを作成する
     *
     * @return RunReportResponse
     * @throws Google\ApiCore\ApiException
     */
    protected function createReport()
    {
        $client = new BetaAnalyticsDataClient();
        try {
            $response = $client->runReport($this->request);
        } finally {
            $client->close();
        }
        return $response;
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
