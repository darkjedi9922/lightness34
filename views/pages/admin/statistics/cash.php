<?php /** @var frame\views\Page $self */

use frame\lists\base\IdentityList;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\stats\CashValueStat;
use frame\tools\JsonEncoder;
use frame\actions\ViewAction;
use frame\database\Records;
use frame\lists\iterators\IdentityIterator;

$routes = new IdentityList(CashRouteStat::class, ['id' => 'DESC']);
$routesProps = [];
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
    $routesProps[] = [
        'route' => $route->route,
        'values' => $cashValues,
        'time' => date('d.m.Y H:i', $route->time)
    ];
}
$historyRouteCount = count($routesProps);
$historyProps = JsonEncoder::forHtmlAttribute([
    'routes' => $routesProps
]);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Мониторинг</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Кеш</span>
    </div>
</div>
<span class="content__title">История вызовов (<?= $historyRouteCount ?>)</span>
<div id="cash-use-history" data-props="<?= $historyProps ?>"></div>