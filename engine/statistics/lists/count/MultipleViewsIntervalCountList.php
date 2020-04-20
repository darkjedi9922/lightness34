<?php namespace engine\statistics\lists\count;

use engine\statistics\lists\MultipleIntervalDataList;

class MultipleViewsIntervalCountList extends MultipleIntervalDataList
{
    protected function getQuery(): string
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        $summaryFunction = $this->getSortField();
        return "SELECT
            stat_views.file as object,
            COUNT(stat_views.id) as value, 
            FLOOR(stat_routes.time / $interval) * $interval as interval_time
        FROM stat_views
            INNER JOIN stat_routes ON stat_views.route_id = stat_routes.id
            INNER JOIN (
                SELECT file, $summaryFunction(count) as sort_field
                FROM (
                    SELECT stat_views.file, COUNT(stat_views.id) as count, 
                        FLOOR(stat_routes.time / $interval) * $interval as interval_time
                    FROM stat_views
                        INNER JOIN stat_routes ON stat_views.route_id = stat_routes.id
                    GROUP BY stat_views.file, interval_time 
                    HAVING interval_time >= $minInterval
                ) as intervalled
                GROUP BY file
                ORDER BY sort_field {$this->getSortOrder()}
                LIMIT {$this->getObjectsLimit()}
            ) as limited ON stat_views.file = limited.file
        GROUP BY stat_views.file, interval_time
        HAVING interval_time >= $minInterval
        ORDER BY interval_time ASC";
    }

    protected function getEmptyValue()
    {
        return 0;
    }
}
