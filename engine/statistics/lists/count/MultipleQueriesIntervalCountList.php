<?php namespace engine\statistics\lists\count;

use engine\statistics\lists\MultipleIntervalDataList;

class MultipleQueriesIntervalCountList extends MultipleIntervalDataList
{
    protected function getQuery(): string
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        $summaryFunction = $this->getSortField();
        return "SELECT
            stat_queries.sql_text as object,
            COUNT(stat_queries.id) as value, 
            FLOOR(stat_routes.time / $interval) * $interval as interval_time
        FROM stat_queries
            INNER JOIN stat_routes ON stat_queries.route_id = stat_routes.id
            INNER JOIN (
                SELECT sql_text, $summaryFunction(count) as sort_field
                FROM (
                    SELECT stat_queries.sql_text, COUNT(stat_queries.id) as count, 
                        FLOOR(stat_routes.time / $interval) * $interval as interval_time
                    FROM stat_queries
                        INNER JOIN stat_routes ON stat_queries.route_id = stat_routes.id
                    GROUP BY stat_queries.sql_text, interval_time 
                    HAVING interval_time >= $minInterval
                ) as intervalled
                GROUP BY sql_text
                ORDER BY sort_field {$this->getSortOrder()}
                LIMIT {$this->getObjectsLimit()}
            ) as limited ON stat_queries.sql_text = limited.sql_text
        GROUP BY stat_queries.sql_text, interval_time
        HAVING interval_time >= $minInterval
        ORDER BY interval_time ASC";
    }

    protected function getEmptyValue()
    {
        return 0;
    }
}
