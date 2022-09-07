<?php

namespace Acms\Plugins\GoogleAnalytics4\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;

/**
 * Analytics Data APIのサービス
 * @see https://developers.google.com/analytics/devguides/reporting/data/v1
 */
class AnalyticsData
{
    public $request = [];

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
     * レポートを作成する
     *
     * @return \Google\Analytics\Data\V1beta\RunReportResponse
     */
    public function createReport()
    {
        $client = new BetaAnalyticsDataClient();
        return $client->runReport($this->request);
    }
}
