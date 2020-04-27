<?php namespace engine\statistics\lists\history;

use frame\database\Records;
use engine\statistics\stats\EventSubscriberStat;
use frame\lists\iterators\IdentityIterator;
use engine\statistics\stats\EventEmitStat;
use engine\statistics\stats\RouteStat;
use frame\stdlib\drivers\database\MySqlDriver;
use frame\route\Router;

class EventsHistoryList extends HistoryList
{
    public function getStatIdentityClass(): string
    {
        return RouteStat::class;
    }

    protected function assembleArray(IdentityIterator $list): array
    {
        $result = [];
        foreach ($list as $routeStat) {
            /** @var RouteStat $routeStat */
            $route = [
                'route' => (new Router($routeStat->url))->pagename,
                'subscribers' => [],
                'emits' => [],
                'handles' => [],
                'time' => date('d.m.Y H:i', $routeStat->time)
            ];

            $subscribers = Records::from(EventSubscriberStat::getTable(), [
                'route_id' => $routeStat->id
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
                'route_id' => $routeStat->id
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

            $handles = $this->loadHandles($routeStat);
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

    private function loadHandles(RouteStat $routeStat): array
    {
        $routeTable = $routeStat::getTable();
        $emitsTable = EventEmitStat::getTable();
        $handlesTable = 'stat_event_emit_handles';
        return MySqlDriver::getDriver()->query(
            "SELECT $handlesTable.*
            FROM 
                $handlesTable 
                INNER JOIN $emitsTable ON $handlesTable.emit_id = $emitsTable.id
                INNER JOIN $routeTable ON $emitsTable.route_id = $routeTable.id
            WHERE $routeTable.id = {$routeStat->id}
            ORDER BY $handlesTable.id ASC"
        )->readAll();
    }
}