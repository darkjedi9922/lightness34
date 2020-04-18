<?php namespace engine\statistics\lists\history;

use engine\statistics\stats\EventRouteStat;
use frame\lists\base\IdentityList;
use frame\database\Records;
use engine\statistics\stats\EventSubscriberStat;
use frame\lists\iterators\IdentityIterator;
use engine\statistics\stats\EventEmitStat;

class EventsHistoryList extends HistoryList
{
    public function getStatIdentityClass(): string
    {
        return EventRouteStat::class;
    }

    protected function assembleArray(IdentityList $list): array
    {
        $result = [];
        foreach ($list as $routeStat) {
            /** @var EventRouteStat $routeStat */
            $route = [
                'route' => $routeStat->route,
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

            $handles = $routeStat->loadHandles();
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
}