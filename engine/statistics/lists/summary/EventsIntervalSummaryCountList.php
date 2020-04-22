<?php namespace engine\statistics\lists\summary;

class EventsIntervalSummaryCountList extends IntervalSummaryCountList
{
    protected function getQuery(int $secondsInterval, int $limit): string
    {
        return "SELECT 
            COUNT(stat_event_emit_handles.id) as value,
            FLOOR(time / $secondsInterval) * $secondsInterval as interval_time
        FROM stat_event_emit_handles 
            INNER JOIN stat_event_emits
                ON stat_event_emit_handles.emit_id = stat_event_emits.id 
            INNER JOIN stat_routes ON stat_event_emits.route_id = stat_routes.id
        GROUP BY interval_time
        ORDER BY interval_time DESC
        LIMIT $limit";
    }
}