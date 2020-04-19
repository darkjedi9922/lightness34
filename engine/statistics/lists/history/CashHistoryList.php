<?php namespace engine\statistics\lists\history;

use engine\statistics\stats\CashValueStat;
use frame\database\Records;
use frame\lists\iterators\IdentityIterator;
use engine\statistics\stats\RouteStat;
use frame\route\Router;

class CashHistoryList extends HistoryList
{
    public function getStatIdentityClass(): string
    {
        return RouteStat::class;
    }

    protected function assembleArray(IdentityIterator $list): array
    {
        $routes = [];
        foreach ($list as $route) {
            /** @var RouteStat $route */
            $cashValues = [];
            $cashValuesIterator = new IdentityIterator(
                Records::from(CashValueStat::getTable(), ['route_id' => $route->id])
                    ->order(['id' => 'ASC'])
                    ->select(),
                CashValueStat::class
            );
            foreach ($cashValuesIterator as $cashValue) {
                /** @var CashValueStat $cashValue */
                $cashValues[] = [
                    'class' => $cashValue->class,
                    'key' => $cashValue->value_key,
                    'initDurationSec' => $cashValue->init_duration_sec,
                    'initError' => $cashValue->init_error,
                    'calls' => $cashValue->call_count
                ];
            }
            $routes[] = [
                'route' => (new Router($route->url))->pagename,
                'values' => $cashValues,
                'time' => date('d.m.Y H:i', $route->time)
            ];
        }

        return $routes;
    }
}