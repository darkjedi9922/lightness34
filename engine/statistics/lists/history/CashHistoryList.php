<?php namespace engine\statistics\lists\history;

use engine\statistics\stats\CashValueStat;
use frame\database\Records;
use frame\lists\iterators\IdentityIterator;
use engine\statistics\stats\RouteStat;
use frame\route\Route;
use Iterator;
use frame\route\Router;

class CashHistoryList extends HistoryList
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
        $routes = RouteStat::getTable();
        $counts = 'stat_cash_counts';
        return "SELECT
            $routes.id as route_id,
            $routes.url as route_url,
            $counts.value_count,
            $counts.call_count,
            $counts.status,
            $routes.time
            FROM $routes INNER JOIN $counts ON $routes.id = $counts.route_id
            ORDER BY $sortField $sortOrder
            LIMIT $offset, $limit";
    }

    protected function assembleArray(Iterator $list): array
    {
        $routes = [];
        foreach ($list as $row) {
            $cashValues = [];
            $cashValuesIterator = new IdentityIterator(
                Records::from(CashValueStat::getTable(), [
                    'route_id' => $row['route_id']
                ])
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
                'route' => Router::getDriver()->parseRoute($row['route_url'])->pagename,
                'values' => $cashValues,
                'time' => date('d.m.Y H:i', $row['time'])
            ];
        }

        return $routes;
    }
}