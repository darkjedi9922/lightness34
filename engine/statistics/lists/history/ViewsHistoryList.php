<?php namespace engine\statistics\lists\history;

use frame\lists\iterators\IdentityIterator;
use frame\database\Records;
use engine\statistics\stats\ViewStat;
use engine\statistics\stats\ViewMetaStat;
use engine\statistics\stats\RouteStat;
use frame\route\Router;
use Iterator;

class ViewsHistoryList extends HistoryList
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
        $countTable = 'stat_view_counts';
        return "SELECT
            $routeTable.id as route_id,
            $routeTable.url as route_url,
            $countTable.view_count,
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
            $routeViews = [];
            $viewsIt = new IdentityIterator(
                Records::from(ViewStat::getTable(), ['route_id' => $row['route_id']])
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
                'route' => (new Router($row['route_url']))->pagename,
                'views' => $routeViews,
                'time' => date('d.m.Y H:i', $row['time'])
            ];
        }

        return $routes;
    }
}