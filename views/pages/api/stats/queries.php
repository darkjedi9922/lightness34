<?php /** @var frame\views\Page $self */

use frame\cash\database;
use frame\tools\JsonEncoder;

$data = database::get()->query(
    "SELECT 
        COUNT(stat_queries.id),
        CAST(stat_query_routes.time / (60*60) AS UNSIGNED) * 60*60 as interval_time
    FROM stat_queries INNER JOIN stat_query_routes 
        ON stat_queries.route_id = stat_query_routes.id
    GROUP BY interval_time"
);

$resultData = [];
while (($line = $data->readLine()) !== null) {
    $resultData[] = [
        'time' => date('d.m.Y H', $line['interval_time']) . 'h',
        'count' => $line['COUNT(stat_queries.id)']
    ];
}

$result = [
    'data' => $resultData
];
echo JsonEncoder::forViewText($result);