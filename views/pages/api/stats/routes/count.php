<?php
use frame\tools\JsonEncoder;
use engine\statistics\lists\MultipleRouteIntervalCountList;

$routesLimit = 5;
$sortField = 'max'; // 'max' or 'avg'
$sortOrder = 'desc'; // 'desc' or 'asc'
$intervalCount = 10;
$secondInterval = MultipleRouteIntervalCountList::HOUR_INTERVAL;

$list = new MultipleRouteIntervalCountList(
    $routesLimit,$intervalCount, $secondInterval, $sortField, $sortOrder
);

echo JsonEncoder::forViewText($list->assembleArray());