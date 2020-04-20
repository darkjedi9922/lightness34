<?php namespace engine\statistics\lists\summary;

class RoutesIntervalSummaryCountList extends IntervalSummaryCountList
{
    protected function getQuery(int $secondsInterval, int $limit): string
    {
        return "SELECT 
            COUNT(id) as value,
            FLOOR(time / $secondsInterval) * $secondsInterval as interval_time
        FROM stat_routes
        GROUP BY interval_time
        ORDER BY interval_time DESC
        LIMIT $limit";
    }
}