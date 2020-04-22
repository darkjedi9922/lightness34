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
            FLOOR(time / $interval) * $interval as interval_time
        FROM stat_actions INNER JOIN (
            SELECT class, $summaryFunction(count) as sort_field
            FROM (
                SELECT class, COUNT(id) as count, 
                    FLOOR(time / $interval) * $interval as interval_time
                FROM stat_actions 
                GROUP BY class, interval_time 
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
