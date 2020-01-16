<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\actions\ViewAction;
use engine\statistics\actions\ClearStatistics;
use frame\lists\iterators\IdentityIterator;
use frame\tools\JsonEncoder;
use frame\database\Records;
use engine\statistics\stats\ViewRouteStat;
use engine\statistics\stats\ViewStat;

Init::accessRight('admin', 'see-logs');

$clear = new ViewAction(ClearStatistics::class, ['module' => 'stat/views']);

$routes = [];
$routesIt = new IdentityIterator(
    Records::from(ViewRouteStat::getTable())
        ->order(['id' => 'DESC'])
        ->select(),
    ViewRouteStat::class
);
foreach ($routesIt as $routeStat) {
    /** @var ViewRouteStat $routeStat */
    $routeViews = [];
    $viewsIt = new IdentityIterator(
        Records::from(ViewStat::getTable(), ['route_id' => $routeStat->id])
            ->order(['id' => 'ASC'])
            ->select(),
        ViewStat::class
    );
    foreach ($viewsIt as $viewStat) {
        /** @var ViewStat $viewStat */
        $routeViews[] = [
            'id' => $viewStat->id,
            'class' => $viewStat->class,
            'name' => $viewStat->name,
            'file' => $viewStat->file,
            'parentId' => $viewStat->parent_id
        ];
    }
    $routes[] = [
        'route' => $routeStat->route,
        'views' => $routeViews
    ];
}

$viewHistoryProps = [
    'routes' => $routes,
    'clearStatsUrl' => $clear->getUrl()
];
$viewHistoryProps = JsonEncoder::forHtmlAttribute($viewHistoryProps);
?>

<div id="views-stat-page" data-props="<?= $viewHistoryProps ?>"></div>