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
        FLOOR(stat_query_routes.time / $secInterval) * $secInterval 
            as interval_time
    FROM stat_queries INNER JOIN stat_query_routes 
        ON stat_queries.route_id = stat_query_routes.id
    GROUP BY interval_time
    ORDER BY interval_time DESC
    LIMIT $maxCount"
);

$resultData = [];
$resultCount = 0;
$lastTime = 0; // Для расчета количества интервальных промежутков между значениями
// Идем по времени с конца в начало.
while (($line = $data->readLine()) !== null) {
    // Заполняем промежуток до следующего значения нулями по количеству
    // интервальных промежутков между текущим и следующим.
    $currentTime = $line['interval_time'];
    if ($lastTime) {
        $times = ($lastTime - $secInterval - $currentTime) / $secInterval;
        for ($i = 0; $i < $times; ++$i) {
            if ($resultCount === $maxCount) break;
            $prevInterval = $lastTime - $secInterval * ($i + 1);
            $resultData[] = [
                // Помним, что идем с конца, значит время уменьшается.
                'time' => date('d.m.Y H', $prevInterval) . 'h',
                'count' => 0
            ];
            $resultCount += 1;
        }
    }

    if ($resultCount === $maxCount) break;

    // Затем добавляем текущее время.
    $resultData[] = [
        'time' => date('d.m.Y H', $line['interval_time']) . 'h',
        'count' => $line['COUNT(stat_queries.id)']
    ];
    $resultCount += 1;

    $lastTime = $currentTime;
}

$result = [
    'data' => array_reverse($resultData)
];
echo JsonEncoder::forViewText($result);