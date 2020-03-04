<?php namespace engine\statistics\lists;

class MultipleRouteIntervalTimeList extends MultipleIntervalDataList
{
    protected function getQuery(): string
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        $summaryFunction = $this->getSortField();
        return "SELECT 
            stat_routes.url as object, 
            ROUND($summaryFunction(duration_sec), 3) as value,
            -- ROUND(AVG(duration_sec), 3) as avg,
            -- MIN(duration_sec) as min,
            FLOOR(time / $interval) * $interval as interval_time
        FROM stat_routes INNER JOIN (
            SELECT url, 
                -- Тут находим значение, по которому будем сортировка.
                $summaryFunction(duration_sec) as sorted_field
            FROM stat_routes
            WHERE (FLOOR(time / $interval) * $interval) >= $minInterval
            GROUP BY url
            ORDER BY sorted_field {$this->getSortOrder()}
            LIMIT {$this->getObjectsLimit()}
        ) as limited ON stat_routes.url = limited.url
        GROUP BY object, interval_time
        HAVING interval_time >= $minInterval
        ORDER BY interval_time ASC";
    }

    protected function getEmptyValue()
    {
        return null;
    }
}