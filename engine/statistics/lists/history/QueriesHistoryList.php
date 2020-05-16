<?php namespace engine\statistics\lists\history;

use frame\lists\iterators\IdentityIterator;
use frame\database\Records;
use engine\statistics\stats\QueryStat;
use engine\statistics\stats\RouteStat;
use frame\route\Router;
use Iterator;

class QueriesHistoryList extends HistoryList
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
        $routeTable = RouteStat::getTable();
        $countTable = 'stat_query_counts';
        return "SELECT
            $routeTable.id as route_id,
            $routeTable.url as route_url,
            $countTable.query_count,
            $countTable.sum_load,
            $countTable.status,
            $routeTable.time
            FROM $routeTable INNER JOIN $countTable
                ON $routeTable.id = $countTable.route_id
            ORDER BY $sortField $sortOrder
            LIMIT $offset, $limit";
    }

    protected function assembleArray(Iterator $list): array
    {
        $routes = [];
        foreach ($list as $row) {
            $route = [
                'route' => (new Router($row['route_url']))->pagename,
                'queries' => [],
                'time' => date('d.m.Y H:i', $row['time'])
            ];

            $routeQueries = new IdentityIterator(
                Records::from(QueryStat::getTable(), [
                    'route_id' => $row['route_id']
                ])
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