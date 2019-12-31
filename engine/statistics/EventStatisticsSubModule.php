<?php namespace engine\statistics;

use frame\Core;
use engine\statistics\stats\EventRouteStat;
use engine\statistics\macros\events\CollectEventSubscribers;
use engine\statistics\macros\events\CollectEventRoute;
use engine\statistics\macros\events\CollectEventEmits;
use engine\statistics\stats\EventEmitStat;
use engine\statistics\stats\EventSubscriberStat;
use frame\database\Records;

class EventStatisticsSubModule extends BaseStatisticsSubModule
{
    public function clearStats()
    {
        Records::select('stat_event_emit_handles')->delete();
        Records::select(EventEmitStat::getTable());
        Records::select(EventSubscriberStat::getTable())->delete();
        Records::select(EventRouteStat::getTable())->delete();
    }

    protected function getAppEventHandlers(): array
    {
        $routeStat = new EventRouteStat;
        $subsciberCollector = new CollectEventSubscribers($routeStat);
        $emitCollector = new CollectEventEmits($routeStat, $subsciberCollector);

        return [
            Core::META_APP_EVENT_SUBSCRIBE => $subsciberCollector,
            Core::META_APP_EVENT_EMIT => $emitCollector,
            Core::EVENT_APP_START => new CollectEventRoute($routeStat)
        ];
    }
}