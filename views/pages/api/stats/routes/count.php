<?php
use frame\cash\database;
use frame\tools\JsonEncoder;
use function lightlib\last;
use engine\statistics\lists\TimeIntervalList;

$limit = 5;
$orderBy = 'max'; // 'max' or 'avg'
$orderAs = 'desc'; // 'desc' or 'asc'
$secondsInterval = 60*60;
$currentInterval = (int)(time() / $secondsInterval) * $secondsInterval;
$minTime = $currentInterval - $secondsInterval * (10 - 1);

$queryResult = database::get()->query(
    "SELECT stat_routes.url, COUNT(stat_routes.id) as count, max, avg, 
        FLOOR(time / $secondsInterval) * $secondsInterval as interval_time
    FROM stat_routes INNER JOIN (
        SELECT url, MAX(count) as max, AVG(count) as avg
        FROM (
            SELECT url, COUNT(id) as count, 
                FLOOR(time / $secondsInterval) * $secondsInterval as interval_time
            FROM `stat_routes` 
            GROUP BY url, interval_time 
            HAVING interval_time >= $minTime
            ORDER BY id ASC
        ) as intervalled
        GROUP BY url
        ORDER BY $orderBy $orderAs
        LIMIT $limit
    ) as stat ON stat_routes.url = stat.url
    GROUP BY stat_routes.url, interval_time
    HAVING interval_time >= $minTime
    ORDER BY interval_time ASC"
);

$routes = [];
while (($line = $queryResult->readLine()) !== null) {
    $currentUrl = $line['url'];
    $currentTime = $line['interval_time'];

    if (!isset($routes[$currentUrl])) {
        $routes[$currentUrl] = [
            'counts' => [],
            'max' => $line['max'],
            'avg' => $line['avg']
        ];
    }
    
    // Заполняем промежуток до следующего значения нулями по количеству
    // интервальных промежутков между текущим и следующим.
    $lastTime = !empty($routes[$currentUrl]['counts'])
        ? last($routes[$currentUrl]['counts'])['timestamp']
        : $minTime - $secondsInterval;
    $times = new TimeIntervalList($lastTime, $currentTime, $secondsInterval);
    foreach ($times as $time) {
        $routes[$currentUrl]['counts'][] = [
            'count' => 0,
            'time' => date('d.m.Y H', $time) . 'h',
            'timestamp' => $time
        ];
    }

    // Затем добавляем текущее время.
    $routes[$currentUrl]['counts'][] =[
        'count' => $line['count'],
        'time' => date('d.m.Y H', $currentTime) . 'h',
        'timestamp' => $currentTime
    ];
}

// Нужно добавить нулевые интервалы от последнего интервала до текущего момента.
foreach ($routes as $url => &$data) {
    $times = new TimeIntervalList(
        last($data['counts'])['timestamp'],
        $currentInterval + $secondsInterval,
        $secondsInterval
    );
    foreach ($times as $time) {
        $data['counts'][] = [
            'count' => 0,
            'time' => date('d.m.Y H', $time) . 'h',
            'timestamp' => $time
        ];
    }
}

echo JsonEncoder::forViewText($routes);