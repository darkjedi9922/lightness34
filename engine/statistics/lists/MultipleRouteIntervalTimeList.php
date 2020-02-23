<?php namespace engine\statistics\lists;

use frame\database\QueryResult;
use frame\cash\database;
use function lightlib\last;

class MultipleRouteIntervalTimeList extends MultipleIntervalDataList
{
    public function assembleArray(): array
    {
        $result = [];
        $queryResult = $this->query();
        $interval = $this->getSecondInterval();
        while (($line = $queryResult->readLine()) !== null) {
            $url = $line['url'];
            $currentTime = $line['interval_time'];
            $lastTime = null;
            if (!isset($result[$url])) {
                $result[$url] = [];
                // Левая граница не включается в TimeIntervalList,
                // поэтому возьмем границу на один интервал перед ней.
                $lastTime = $this->getLeftBorder() - $interval;
            } else {
                $lastTime = last($result[$url])['timestamp'];
            }

            foreach (
                // Заполняем промежуток до текущего значения нулями по количеству
                // интервальных промежутков между текущим и следующим.
                new TimeIntervalList($lastTime, $currentTime, $interval) 
                as $timestamp
            ) {
                $result[$url][] = [
                    'value' => null,
                    'time' => $this->getIntervalDate($timestamp),
                    'timestamp' => $timestamp
                ];
            }

            // Затем добавляем текущее время.
            $result[$url][] = [
                'value' => $line['value'],
                'time' => $this->getIntervalDate($currentTime),
                'timestamp' => $currentTime
            ];
        }

        // Нужно добавить нулевые интервалы от последнего интервала 
        // до текущего момента.
        foreach ($result as &$data) {
            foreach (
                new TimeIntervalList(
                    last($data)['timestamp'],
                    // Правая гранциа не включается в TimeIntervalList, поэтому берем
                    // границу на интервал после нее.
                    $this->getRightBorder() + $this->getSecondInterval(),
                    $this->getSecondInterval()
                )
                as $timestamp
            ) {
                $data[] = [
                    'value' => null,
                    'time' => $this->getIntervalDate($timestamp),
                    'timestamp' => $timestamp
                ];
            }
        }

        return $result;
    }

    protected function query(): QueryResult
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        $a = $this->getIntervalDate($minInterval);
        return database::get()->query(
            "SELECT stat_routes.url, 
                MAX(duration_sec) as value,
                -- ROUND(AVG(duration_sec), 3) as avg,
                -- MIN(duration_sec) as min,
                FLOOR(time / $interval) * $interval as interval_time
            FROM stat_routes INNER JOIN (
                SELECT url, 
                    -- Тут находим значение, по которому будем сортировка.
                    MAX(duration_sec) as sorted_value
                FROM stat_routes
                WHERE (FLOOR(time / $interval) * $interval) >= $minInterval
                GROUP BY url
                ORDER BY sorted_value DESC
                LIMIT {$this->getObjectsLimit()}
            ) as limited ON stat_routes.url = limited.url
            GROUP BY url, interval_time
            HAVING interval_time >= $minInterval
            ORDER BY interval_time ASC"
        );
    }
}