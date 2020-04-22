<?php namespace engine\statistics\lists\duration;

use engine\statistics\lists\MultipleIntervalDataList;

class MultipleViewsIntervalTimeList extends MultipleIntervalDataList
{
    protected function getQuery(): string
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        $summaryFunction = $this->getSortField();
        return "SELECT 
            stat_views.file as object, 
            ROUND($summaryFunction(stat_views.duration_sec), 3) as value,
            -- ROUND(AVG(duration_sec), 3) as avg,
            -- MIN(duration_sec) as min,
            FLOOR(stat_routes.time / $interval) * $interval as interval_time
        FROM stat_views
            INNER JOIN stat_routes ON stat_views.route_id = stat_routes.id
            INNER JOIN (
                SELECT stat_views.file, 
                    -- Тут находим значение, по которому будем сортировка.
                    $summaryFunction(stat_views.duration_sec) as sorted_field
                FROM stat_views
                    INNER JOIN stat_routes ON stat_views.route_id = stat_routes.id
                WHERE (FLOOR(stat_routes.time / $interval) * $interval) >= $minInterval
                GROUP BY stat_views.file
                ORDER BY sorted_field {$this->getSortOrder()}
                LIMIT {$this->getObjectsLimit()}
            ) as limited ON stat_views.file = limited.file
        GROUP BY object, interval_time
        HAVING interval_time >= $minInterval
        ORDER BY interval_time ASC";
    }

    protected function getEmptyValue()
    {
        return null;
    }
}
