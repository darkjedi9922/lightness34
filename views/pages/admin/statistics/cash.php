<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\CashRouteStat;
use frame\tools\JsonEncoder;
use frame\actions\ViewAction;
use engine\statistics\actions\ClearStatistics;

Init::accessRight('admin', 'see-logs');

$clear = new ViewAction(ClearStatistics::class, ['module' => 'stat/cash']);

$routes = new IdentityList(CashRouteStat::class, ['id' => 'DESC']);
$routesProps = [];
foreach ($routes as $route) {
    /** @var CashRouteStat $route */
    $routesProps[] = [
        'route' => $route->route,
        'usedCashValues' => 0,
        'cashCalls' => 0,
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