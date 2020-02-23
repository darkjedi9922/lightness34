<?php namespace engine\statistics\lists;

use frame\cash\database;
use frame\database\QueryResult;
use function lightlib\last;

class MultipleRouteIntervalCountList extends MultipleIntervalDataList
{
    public function assembleArray(): array
    {
        $queryResult = $this->query();
        $interval = $this->getSecondInterval();
        $result = [];
        while (($line = $queryResult->readLine()) !== null) {
            $currentUrl = $line['url'];
            $currentTime = $line['interval_time'];

            if (!isset($result[$currentUrl])) $result[$currentUrl] = [];

            // Заполняем промежуток до следующего значения нулями по количеству
            // интервальных промежутков между текущим и следующим.
            $lastTime = !empty($result[$currentUrl])
                ? last($result[$currentUrl])['timestamp']
                // Левая граница не включается в TimeIntervalList,
                // поэтому возьмем границу на один интервал перед ней.
                : $this->getLeftBorder() - $interval;
            $times = new TimeIntervalList($lastTime, $currentTime, $interval);
            foreach ($times as $time) {
                $result[$currentUrl][] = [
                    'value' => 0,
                    'time' => $this->getIntervalDate($time),
                    'timestamp' => $time
                ];
            }

            // Затем добавляем текущее время.
                'time' => $this->getIntervalDate($time),
            $result[$currentUrl][] = [
                'value' => $line['count'],
                'timestamp' => $currentTime
            ];
        }

        // Нужно добавить нулевые интервалы от последнего интервала 
        // до текущего момента.
        foreach ($result as &$data) {
            $times = new TimeIntervalList(
                last($data)['timestamp'],
                // Правая гранциа не включается в TimeIntervalList, поэтому берем
                // границу на интервал после нее.
                $this->getRightBorder() + $this->getSecondInterval(),
                $this->getSecondInterval()
            );
            foreach ($times as $time) {
                $data[] = [
                    'value' => 0,
                    'time' => $this->getIntervalDate($time),
                    'timestamp' => $time
                ];
            }
        }

        return $result;
    }

    protected function query(): QueryResult
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        return database::get()->query(
            "SELECT stat_routes.url, COUNT(stat_routes.id) as count, 
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
            ORDER BY interval_time ASC"
        );
    }
}