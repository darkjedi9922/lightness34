<?php namespace engine\statistics\lists\count;

use engine\statistics\lists\MultipleIntervalDataList;

class MultipleActionsIntervalCountList extends MultipleIntervalDataList
{
    protected function getQuery(): string
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        $summaryFunction = $this->getSortField();
        return "SELECT
            stat_actions.class as object,
            COUNT(stat_actions.id) as value, 
            FLOOR(stat_routes.time / $interval) * $interval as interval_time
        FROM stat_actions
            INNER JOIN stat_routes ON stat_actions.route_id = stat_routes.id
            INNER JOIN (
                SELECT class, $summaryFunction(count) as sort_field
                FROM (
                    SELECT stat_actions.class, COUNT(stat_actions.id) as count, 
                        FLOOR(stat_routes.time / $interval) * $interval as interval_time
                    FROM stat_actions
                        INNER JOIN stat_routes ON stat_actions.route_id = stat_routes.id
                    GROUP BY stat_actions.class, interval_time 
                    HAVING interval_time >= $minInterval
                ) as intervalled
                GROUP BY class
                ORDER BY sort_field {$this->getSortOrder()}
                LIMIT {$this->getObjectsLimit()}
            ) as limited ON stat_actions.class = limited.class
        GROUP BY stat_actions.class, interval_time
        HAVING interval_time >= $minInterval
        ORDER BY interval_time ASC";
    }

    protected function getEmptyValue()
    {
        return 0;
    }
}
