<?php namespace engine\statistics\lists\count;

use engine\statistics\lists\MultipleIntervalDataList;

class MultipleEventsIntervalCountList extends MultipleIntervalDataList
{
    protected function getQuery(): string
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        $summaryFunction = $this->getSortField();
        return "SELECT
            stat_event_subscribers.class as object,
            COUNT(stat_event_subscribers.id) as value, 
            FLOOR(stat_routes.time / $interval) * $interval as interval_time
        FROM
            stat_event_subscribers 
            INNER JOIN stat_event_emit_handles 
                ON stat_event_subscribers.id = stat_event_emit_handles.subscriber_id
            INNER JOIN stat_event_emits
                ON stat_event_emit_handles.emit_id = stat_event_emits.id
            INNER JOIN stat_routes ON stat_event_emits.route_id = stat_routes.id
            INNER JOIN (
                SELECT class, $summaryFunction(count) as sort_field
                FROM (
                    SELECT stat_event_subscribers.class, 
                        COUNT(stat_event_subscribers.id) as count, 
                        FLOOR(stat_routes.time / $interval) * $interval as interval_time
                    FROM
                        stat_event_subscribers 
                        INNER JOIN stat_event_emit_handles 
                            ON stat_event_subscribers.id = stat_event_emit_handles.subscriber_id
                        INNER JOIN stat_event_emits
                            ON stat_event_emit_handles.emit_id = stat_event_emits.id
                        INNER JOIN stat_routes ON stat_event_emits.route_id = stat_routes.id
                    GROUP BY stat_event_subscribers.class, interval_time 
                    HAVING interval_time >= $minInterval
                ) as intervalled
                GROUP BY class
                ORDER BY sort_field {$this->getSortOrder()}
                LIMIT {$this->getObjectsLimit()}
            ) as limited ON stat_event_subscribers.class = limited.class
        GROUP BY stat_event_subscribers.class, interval_time
        HAVING interval_time >= $minInterval
        ORDER BY interval_time ASC";
    }

    protected function getEmptyValue()
    {
        return 0;
    }
}