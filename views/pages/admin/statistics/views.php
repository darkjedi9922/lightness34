<?php /** @var frame\views\Page $self */

use frame\actions\ViewAction;
use frame\lists\iterators\IdentityIterator;
use frame\tools\JsonEncoder;
use frame\database\Records;
use engine\statistics\stats\ViewRouteStat;
use engine\statistics\stats\ViewStat;
use engine\statistics\stats\ViewMetaStat;

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
        $meta = [];
        $metaIt = new IdentityIterator(
            Records::from(ViewMetaStat::getTable(), ['view_id' => $viewStat->id])
                ->order(['id' => 'ASC'])
                ->select(),
            ViewMetaStat::class
        );
        foreach ($metaIt as $metaStat) {
            /** @var ViewMetaStat $metaStat */
            $meta[] = [
                'name' => $metaStat->name,
                'value' => $metaStat->value,
                'type' => $metaStat->type
            ];
        }
        $routeViews[] = [
            'id' => $viewStat->id,
            'class' => $viewStat->class,
            'name' => $viewStat->name,
            'file' => $viewStat->file,
            'layoutName' => $viewStat->layout_name,
            'parentId' => $viewStat->parent_id,
            'error' => $viewStat->error,
            'durationSec' => $viewStat->duration_sec,
            'meta' => $meta
        ];
    }
    $routes[] = [
        'route' => $routeStat->route,
        'views' => $routeViews,
        'time' => date('d.m.Y H:i', $routeStat->time)
    ];
}

$viewHistoryProps = [
    'routes' => $routes,
];
$viewHistoryProps = JsonEncoder::forHtmlAttribute($viewHistoryProps);
?>

<div id="views-stat-page" data-props="<?= $viewHistoryProps ?>"></div>