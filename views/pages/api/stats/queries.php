<?php /** @var frame\views\Page $self */

use frame\cash\database;
use frame\tools\JsonEncoder;

$secInterval = 60*60; // 1 hour
$maxCount = 10; // Не должно быть <= 0 или слишком большим

// Чтобы получить целое время запроса округленное к интервалу, нужно сначала
// разделить его на время интервала, отбросив получившуюся дробную часть, и
// умножить на то же время интервала, чтобы заполнить ту часть нулями целой
// части.
$data = database::get()->query(
    "SELECT 
        COUNT(stat_queries.id),
        CAST(stat_query_routes.time / $secInterval AS UNSIGNED) * $secInterval 
            as interval_time
    FROM stat_queries INNER JOIN stat_query_routes 
        ON stat_queries.route_id = stat_query_routes.id
    GROUP BY interval_time
    ORDER BY interval_time DESC
    LIMIT 10"
);

$resultData = [];
$resultCount = 0;
$lastTime = 0; // Для расчета количества интервальных промежутков между значениями
// Идем по времени с конца в начало.
while (($line = $data->readLine()) !== null) {
    if ($resultCount === $maxCount) break;
    
    // Сначала добавляем текущее время.
    $resultData[] = [
        'time' => date('d.m.Y H', $line['interval_time']) . 'h',
        'count' => $line['COUNT(stat_queries.id)']
    ];
    $resultCount += 1;

    // Затем заполняем промежуток до следующего значения нулями по количеству
    // интервальных промежутков между текущим и следующим.
    $currentTime = $line['interval_time'];
    if ($lastTime) {
        $times = ($lastTime - $secInterval - $currentTime) / $secInterval;
        for ($i = 0; $n = $times; ++$i) {
            if ($resultCount === $maxCount) break;
            $resultData[] = [
                // Помним, что идем с конца, значит время уменьшается.
                'time' => date('d.m.Y H', $currentTime - $secInterval * ($i + 1)) . 'h',
                'count' => 0
            ];
            $resultCount += 1;
        }
    }
    $lastTime = $currentTime;
}

$result = [
    'data' => array_reverse($resultData)
];
echo JsonEncoder::forViewText($result);