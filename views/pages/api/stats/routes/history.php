<?php
use frame\tools\JsonEncoder;
use frame\database\Records;
use frame\lists\base\IdentityList;
use frame\actions\ViewAction;
use frame\lists\iterators\IdentityIterator;
use engine\statistics\actions\ClearStatistics;
use engine\statistics\stats\RouteStat;
use engine\statistics\stats\DynamicRouteParam;
use frame\route\Router;

$routeList = new IdentityList(RouteStat::class, ['id' => 'DESC']);

$routes = [];
foreach ($routeList as $route) {
    /** @var RouteStat $route */
    $router = new Router($route->url);
    $type = null;
    switch ($route->type) {
        case $route::ROUTE_TYPE_PAGE: $type = 'page'; break;
        case $route::ROUTE_TYPE_ACTION: $type = 'action'; break;
        case $route::ROUTE_TYPE_DYNAMIC_PAGE: $type = 'dynamic'; break;
    }

    $args = [];
    foreach ($router->args as $key => $value) $args[$key] = $value;

    $dynamicParams = [];
    $dynamicParamsIterator = new IdentityIterator(
        Records::from(DynamicRouteParam::getTable(), ['route_id' => $route->id])
            ->select(),
        DynamicRouteParam::class
    );
    foreach ($dynamicParamsIterator as $param) {
        /** @var DynamicRouteParam $param */
        $dynamicParams[] = $param->value;
    }

    $routes[] = [
        'route' => $router->pagename,
        'ajax' => (bool) $route->ajax,
        'type' => $type,
        'loadSeconds' => $route->duration_sec,
        'time' => date('d.m.Y H:i', $route->time),
        'viewfile' => $route->viewfile,
        'args' => $args,
        'dynamicParams' => $dynamicParams,
        'code' => $route->code,
        'codeInfo' => $route->code_info
    ];
}

$clear = new ViewAction(ClearStatistics::class, ['module' => 'stat/routes']);

echo JsonEncoder::forViewText([
    'routes' => $routes,
    'clearActionUrl' => $clear->getUrl()
]);