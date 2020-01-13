<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\stats\CashValueStat;
use frame\tools\JsonEncoder;
use frame\actions\ViewAction;
use engine\statistics\actions\ClearStatistics;
use frame\cash\database;

Init::accessRight('admin', 'see-logs');

$clear = new ViewAction(ClearStatistics::class, ['module' => 'stat/cash']);

$routes = new IdentityList(CashRouteStat::class, ['id' => 'DESC']);
$routesProps = [];
foreach ($routes as $route) {
    /** @var CashRouteStat $route */
    $cashValuesTable = CashValueStat::getTable();
    $counts = database::get()->query(
        "SELECT COUNT(id), SUM(call_count) 
        FROM `$cashValuesTable` WHERE route_id = {$route->id}"
    )->readLine();
    $routesProps[] = [
        'route' => $route->route,
        'usedCashValues' => $counts['COUNT(id)'],
        'cashCalls' => $counts['SUM(call_count)'] ?? 0,
        'time' => date('d.m.Y H:i', $route->time)
    ];
}
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
    <a href="<?= $clear->getUrl() ?>" class="button">Очистить статистику</a>
</div>
<span class="content__title">История вызовов</span>
<div id="cash-use-history" data-props="<?= $historyProps ?>"></div>