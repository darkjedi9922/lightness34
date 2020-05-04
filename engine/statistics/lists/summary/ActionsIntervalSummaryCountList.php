<?php namespace engine\statistics\lists\summary;

class ActionsIntervalSummaryCountList extends IntervalSummaryCountList
{
    protected function getQuery(int $secondsInterval, int $limit): string
    {
        return "SELECT 
            COUNT(stat_actions.id) as value,
            FLOOR(stat_routes.time / $secondsInterval) * $secondsInterval as interval_time
        FROM stat_actions
            INNER JOIN stat_routes ON stat_actions.route_id = stat_routes.id 
        GROUP BY interval_time
        ORDER BY interval_time DESC
        LIMIT $limit";
    }
}