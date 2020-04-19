<?php namespace engine\statistics\lists\history;

use frame\lists\iterators\IdentityIterator;
use frame\database\Records;
use engine\statistics\stats\QueryStat;
use engine\statistics\stats\RouteStat;
use frame\route\Router;

class QueriesHistoryList extends HistoryList
{
    public function getStatIdentityClass(): string
    {
        return RouteStat::class;
    }

    protected function assembleArray(IdentityIterator $list): array
    {
        $routes = [];
        foreach ($list as $routeStat) {
            /** @var RouteStat $routeStat */
            $route = [
                'route' => (new Router($routeStat->url))->pagename,
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
                    'error' => $queryStat->error,
                    'durationSec' => $queryStat->duration_sec
                ];
            }

            $routes[] = $route;
        }
        return $routes;
    }
}