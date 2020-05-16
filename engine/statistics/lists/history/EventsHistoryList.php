<?php namespace engine\statistics\lists\history;

use frame\database\Records;
use engine\statistics\stats\EventSubscriberStat;
use frame\lists\iterators\IdentityIterator;
use engine\statistics\stats\EventEmitStat;
use engine\statistics\stats\RouteStat;
use frame\database\SqlDriver;
use frame\route\Router;
use Iterator;

class EventsHistoryList extends HistoryList
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
        $routeTable = 'stat_routes';
        $countTable = 'stat_event_counts';
        return "SELECT
            $routeTable.id as route_id,
            $routeTable.url as route_url,
            $countTable.subscriber_count,
            $countTable.emit_count,
            $countTable.handle_count,
            $routeTable.time
        FROM $routeTable INNER JOIN $countTable
            ON $routeTable.id = $countTable.route_id
        ORDER BY $sortField $sortOrder
        LIMIT $offset, $limit";
    }

    protected function assembleArray(Iterator $list): array
    {
        $result = [];
        foreach ($list as $row) {
            // $routeStat = new RouteStat($row);
            $route = [
                'route' => Router::getDriver()->parseRoute($row['route_url'])->pagename,
                'subscribers' => [],
                'emits' => [],
                'handles' => [],
                'time' => date('d.m.Y H:i', $row['time'])
            ];

            $subscribers = Records::from(EventSubscriberStat::getTable(), [
                'route_id' => $row['route_id']
            ])->order(['id' => 'ASC'])->select();
            $subscribersIt = new IdentityIterator($subscribers, EventSubscriberStat::class);
            foreach ($subscribersIt as $subscriberStat) {
                /** @var EventSubscriberStat $subscriberStat */
                $subscriber = [
                    'id' => $subscriberStat->id,
                    'event' => $subscriberStat->event,
                    'class' => $subscriberStat->class
                ];
                $route['subscribers'][] = $subscriber;
            }

            $emits = Records::from(EventEmitStat::getTable(), [
                'route_id' => $row['route_id']
            ])->order(['id' => 'ASC'])->select();
            $emitsIt = new IdentityIterator($emits, EventEmitStat::class);
            foreach ($emitsIt as $emitStat) {
                /** @var EventEmitStat $emitStat */
                $route['emits'][] = [
                    'id' => $emitStat->id,
                    'event' => $emitStat->event,
                    'argsJson' => $emitStat->args_json
                ];
            }

            $handles = $this->loadHandles($row['route_id']);
            foreach ($handles as $handle) {
                $route['handles'][] = [
                    'emitId' => $handle['emit_id'],
                    'subscriberId' => $handle['subscriber_id'],
                    'durationSec' => $handle['duration_sec']
                ];
            }

            $result[] = $route;
        }

        return $result;
    }

    private function loadHandles(int $routeStatId): array
    {
        $routeTable = RouteStat::getTable();
        $emitsTable = EventEmitStat::getTable();
        $handlesTable = 'stat_event_emit_handles';
        return SqlDriver::getDriver()->query(
            "SELECT $handlesTable.*
            FROM 
                $handlesTable 
                INNER JOIN $emitsTable ON $handlesTable.emit_id = $emitsTable.id
                INNER JOIN $routeTable ON $emitsTable.route_id = $routeTable.id
            WHERE $routeTable.id = $routeStatId
            ORDER BY $handlesTable.id ASC"
        )->readAll();
    }
}