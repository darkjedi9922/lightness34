<?php

use frame\lists\base\IdentityList;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\stats\CashValueStat;
use frame\tools\JsonEncoder;
use frame\database\Records;
use frame\lists\iterators\IdentityIterator;

$routes = new IdentityList(CashRouteStat::class, ['id' => 'DESC']);
$routes = [];
foreach ($routes as $route) {
    /** @var CashRouteStat $route */
    $cashValues = [];
    $cashValuesIterator = new IdentityIterator(
        Records::from(CashValueStat::getTable(), ['route_id' => $route->id])
            ->order(['id' => 'ASC'])
            ->select(),
        CashValueStat::class
    );
    foreach ($cashValuesIterator as $cashValue) {
        /** @var CashValueStat $cashValue */
        $cashValues[] = [
            'class' => $cashValue->class,
            'key' => $cashValue->value_key,
            'initDurationSec' => $cashValue->init_duration_sec,
            'initError' => $cashValue->init_error,
            'calls' => $cashValue->call_count
        ];
    }
    $routes[] = [
        'route' => $route->route,
        'values' => $cashValues,
        'time' => date('d.m.Y H:i', $route->time)
    ];
}

echo JsonEncoder::forViewText($routes);