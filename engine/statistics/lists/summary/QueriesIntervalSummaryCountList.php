<?php namespace engine\statistics\lists\summary;

class QueriesIntervalSummaryCountList extends IntervalSummaryCountList
{
    protected function getQuery(int $secondsInterval, int $limit): string
    {
        return "SELECT 
            COUNT(stat_queries.id) as value,
            FLOOR(stat_routes.time / $secondsInterval) * $secondsInterval as interval_time
        FROM stat_queries
            INNER JOIN stat_routes ON stat_queries.route_id = stat_routes.id 
        GROUP BY interval_time
        ORDER BY interval_time DESC
        LIMIT $limit";
    }
}
