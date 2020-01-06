<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\lists\base\IdentityList;
use engine\statistics\stats\QueryRouteStat;
use frame\lists\iterators\IdentityIterator;
use frame\database\Records;
use engine\statistics\stats\QueryStat;
use frame\actions\ViewAction;
use engine\statistics\actions\ClearStatistics;

Init::accessRight('admin', 'see-logs');

$queryHistoryProps = ['routes' => []];

$queryRoutes = new IdentityList(QueryRouteStat::class, ['id' => 'DESC']);
foreach ($queryRoutes as $routeStat) {
    /** @var QueryRouteStat $routeStat */
    $route = [
        'route' => $routeStat->route,
        'queries' => [],
        'time' => date('d.m.Y H:i', $routeStat->time)
    ];

    $routeQueries = new IdentityIterator(
        Records::from(QueryStat::getTable(), ['route_id' => $routeStat->id])
            ->order(['id' => 'ASC'])
            ->select(),
        QueryStat::class
    );
    foreach ($routeQueries as $queryStat) {
        /** @var QueryStat $queryStat */
        $route['queries'][] = [
            'sql' => $queryStat->sql_text,
            'durationSec' => $queryStat->duration_sec
        ];
    }

    $queryHistoryProps['routes'][] = $route;
}

$clear = new ViewAction(ClearStatistics::class, ['module' => 'stat/db']);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Мониторинг</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">База данных</span>
    </div>
    <a href="<?= $clear->getUrl() ?>" class="button">Очистить статистику</a>
</div>

<span class="content__title">История запросов</span>
<div id="query-history" data-props='<?= json_encode($queryHistoryProps, JSON_HEX_AMP) ?>'></div>