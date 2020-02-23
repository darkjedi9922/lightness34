<?php
use frame\tools\JsonEncoder;
use engine\statistics\lists\MultipleRouteIntervalTimeList;

$routesLimit = 5;
$sortField = 'max'; // 'max' or 'avg'
$sortOrder = 'desc'; // 'desc' or 'asc'
$intervalCount = 10;
$secondInterval = MultipleRouteIntervalTimeList::HOUR_INTERVAL;

$list = new MultipleRouteIntervalTimeList(
    $routesLimit, $intervalCount, $secondInterval, $sortField, $sortOrder
);

echo JsonEncoder::forViewText($list->assembleArray());