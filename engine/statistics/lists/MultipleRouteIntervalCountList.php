<?php namespace engine\statistics\lists;

use frame\cash\database;
use frame\database\QueryResult;
use ArrayIterator;

use function lightlib\last;

class MultipleRouteIntervalCountList extends TimeIntervalList
{
    const SORT_FIELD_MAX = 'max';
    const SORT_FIELD_AVG = 'avg';
    const SORT_ORDER_DESC = 'desc';
    const SORT_ORDER_ASC = 'asc';

    private $routesLimit;
    private $sortField;
    private $sortOrder;

    public function __construct(
        int $routesLimit,
        int $intervalCount,
        int $secondInterval,
        string $sortField,
        string $sortOrder
    ) {
        $currentInterval = (int)(time() / $secondInterval) * $secondInterval;
        $minInterval = $currentInterval - $secondInterval * ($intervalCount - 1);
        parent::__construct($minInterval, $currentInterval, $secondInterval);
        $this->routesLimit = $routesLimit;
        $this->sortField = $sortField;
        $this->sortOrder = $sortOrder;
    }

    public function getIterator()
    {        
        return new ArrayIterator($this->assembleArray());
    }

    public function assembleArray(): array
    {
        $queryResult = $this->query();
        $interval = $this->getSecondInterval();
        $result = [];
        while (($line = $queryResult->readLine()) !== null) {
            $currentUrl = $line['url'];
            $currentTime = $line['interval_time'];

            if (!isset($result[$currentUrl])) {
                $result[$currentUrl] = [
                    'counts' => [],
                    'max' => $line['max'],
                    'avg' => $line['avg']
                ];
            }

            // Заполняем промежуток до следующего значения нулями по количеству
            // интервальных промежутков между текущим и следующим.
            $lastTime = !empty($result[$currentUrl]['counts'])
                ? last($result[$currentUrl]['counts'])['timestamp']
                // Левая граница не включается в TimeIntervalList,
                // поэтому возьмем границу на один интервал перед ней.
                : $this->getLeftBorder() - $interval;
            $times = new TimeIntervalList($lastTime, $currentTime, $interval);
            foreach ($times as $time) {
                $result[$currentUrl]['counts'][] = [
                    'count' => 0,
                    'time' => $this->getIntervalDate($time),
                    'timestamp' => $time
                ];
            }

            // Затем добавляем текущее время.
            $result[$currentUrl]['counts'][] = [
                'count' => $line['count'],
                'time' => $this->getIntervalDate($time),
                'timestamp' => $currentTime
            ];
        }

        // Нужно добавить нулевые интервалы от последнего интервала 
        // до текущего момента.
        foreach ($result as &$data) {
            $times = new TimeIntervalList(
                last($data['counts'])['timestamp'],
                // Правая гранциа не включается в TimeIntervalList, поэтому берем
                // границу на интервал после нее.
                $this->getRightBorder() + $this->getSecondInterval(),
                $this->getSecondInterval()
            );
            foreach ($times as $time) {
                $data['counts'][] = [
                    'count' => 0,
                    'time' => $this->getIntervalDate($time),
                    'timestamp' => $time
                ];
            }
        }

        return $result;
    }

    private function query(): QueryResult
    {
        $interval = $this->getSecondInterval();
        $minInterval = $this->getLeftBorder();
        return database::get()->query(
            "SELECT stat_routes.url, COUNT(stat_routes.id) as count, max, avg, 
                FLOOR(time / $interval) * $interval as interval_time
            FROM stat_routes INNER JOIN (
                SELECT url, MAX(count) as max, AVG(count) as avg
                FROM (
                    SELECT url, COUNT(id) as count, 
                        FLOOR(time / $interval) * $interval as interval_time
                    FROM `stat_routes` 
                    GROUP BY url, interval_time 
                    HAVING interval_time >= $minInterval
                    ORDER BY id ASC
                ) as intervalled
                GROUP BY url
                ORDER BY {$this->sortField} {$this->sortOrder}
                LIMIT {$this->routesLimit}
            ) as stat ON stat_routes.url = stat.url
            GROUP BY stat_routes.url, interval_time
            HAVING interval_time >= $minInterval
            ORDER BY interval_time ASC"
        );
    }
}