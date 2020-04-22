<?php namespace engine\statistics\lists\duration;

use engine\statistics\lists\MultipleIntervalDataList;

class MultipleCashIntervalTimeList extends MultipleIntervalDataList
{
    protected function getQuery(): string
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        $summaryFunction = $this->getSortField();
        return "SELECT 
            stat_cash_values.class as object, 
            ROUND($summaryFunction(stat_cash_values.init_duration_sec), 3) as value,
            -- ROUND(AVG(duration_sec), 3) as avg,
            -- MIN(duration_sec) as min,
            FLOOR(stat_routes.time / $interval) * $interval as interval_time
        FROM stat_cash_values
            INNER JOIN stat_routes ON stat_cash_values.route_id = stat_routes.id
            INNER JOIN (
                SELECT stat_cash_values.class, 
                    -- Тут находим значение, по которому будем сортировка.
                    $summaryFunction(stat_cash_values.init_duration_sec) as sorted_field
                FROM stat_cash_values
                    INNER JOIN stat_routes ON stat_cash_values.route_id = stat_routes.id
                WHERE (FLOOR(stat_routes.time / $interval) * $interval) >= $minInterval
                GROUP BY stat_cash_values.class
                ORDER BY sorted_field {$this->getSortOrder()}
                LIMIT {$this->getObjectsLimit()}
            ) as limited ON stat_cash_values.class = limited.class
        GROUP BY object, interval_time
        HAVING interval_time >= $minInterval
        ORDER BY interval_time ASC";
    }

    protected function getEmptyValue()
    {
        return null;
    }
}
