<?php namespace engine\statistics\lists\history;

use engine\statistics\stats\RouteStat;
use frame\database\Records;
use frame\lists\iterators\IdentityIterator;
use engine\statistics\stats\DynamicRouteParam;
use frame\route\Router;
use Iterator;

class RoutesHistoryList extends HistoryList
{
    protected function queryCountAll(): int
    {
        return Records::from(RouteStat::getTable())->count('id');
    }

    protected function getSqlQuery(
        string $sortField,
        string $sortOrder,
        int $offset,
        int $limit
    ): string {
        $table = RouteStat::getTable();
        return "SELECT * FROM $table
            ORDER BY $sortField $sortOrder
            LIMIT $offset, $limit";
    }

    protected function assembleArray(Iterator $list): array
    {
        $routes = [];
        foreach ($list as $row) {
            $route = new RouteStat($row);
            $router = new Router($route->url);
            $type = null;
            switch ($route->type) {
                case $route::ROUTE_TYPE_PAGE:
                    $type = 'page';
                    break;
                case $route::ROUTE_TYPE_ACTION:
                    $type = 'action';
                    break;
                case $route::ROUTE_TYPE_DYNAMIC_PAGE:
                    $type = 'dynamic';
                    break;
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
                'ajax' => (bool)$route->ajax,
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

        return $routes;
    }
}