<?php namespace engine\statistics\lists\history;

use frame\lists\iterators\IdentityIterator;
use frame\database\Records;
use engine\statistics\stats\ViewStat;
use engine\statistics\stats\ViewMetaStat;
use engine\statistics\stats\RouteStat;
use frame\route\Router;

class ViewsHistoryList extends HistoryList
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
                'route' => (new Router($routeStat->url))->pagename,
                'views' => $routeViews,
                'time' => date('d.m.Y H:i', $routeStat->time)
            ];
        }

        return $routes;
    }
}