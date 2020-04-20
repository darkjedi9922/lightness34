<?php namespace engine\statistics\lists\summary;

class CashIntervalSummaryCountList extends IntervalSummaryCountList
{
    protected function getQuery(int $secondsInterval, int $limit): string
    {
        return "SELECT 
            CAST(SUM(stat_cash_values.call_count) AS INTEGER) as value,
            FLOOR(stat_routes.time / $secondsInterval) * $secondsInterval as interval_time
        FROM stat_cash_values
            INNER JOIN stat_routes ON stat_cash_values.route_id = stat_routes.id 
        GROUP BY interval_time
        ORDER BY interval_time DESC
        LIMIT $limit";
    }
}
