<?php namespace engine\statistics\lists;

class MultipleRouteIntervalCountList extends MultipleIntervalDataList
{
    protected function getQuery(): string
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        return "SELECT
            stat_routes.url as object,
            COUNT(stat_routes.id) as value, 
            FLOOR(time / $interval) * $interval as interval_time
        FROM stat_routes INNER JOIN (
            SELECT url, MAX(count) as max -- max - поле, по которому сортируем.
            FROM (
                SELECT url, COUNT(id) as count, 
                    FLOOR(time / $interval) * $interval as interval_time
                FROM `stat_routes` 
                GROUP BY url, interval_time 
                HAVING interval_time >= $minInterval
                ORDER BY id ASC
            ) as intervalled
            GROUP BY url
            ORDER BY {$this->getSortField()} {$this->getSortOrder()}
            LIMIT {$this->getObjectsLimit()}
        ) as limited ON stat_routes.url = limited.url
        GROUP BY stat_routes.url, interval_time
        HAVING interval_time >= $minInterval
        ORDER BY interval_time ASC";
    }

    protected function getEmptyValue()
    {
        return 0;
    }
}