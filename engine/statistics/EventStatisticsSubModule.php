<?php namespace engine\statistics;

use frame\Core;
use engine\statistics\stats\EventRouteStat;
use engine\statistics\macros\events\CollectEventHistory;
use engine\statistics\stats\EventEmitStat;
use engine\statistics\stats\EventSubscriberStat;
use frame\database\Records;

class EventStatisticsSubModule extends BaseStatisticsSubModule
{
    public function clearStats()
    {
        Records::select('stat_event_emit_handles')->delete();
        Records::select(EventEmitStat::getTable())->delete();
        Records::select(EventSubscriberStat::getTable())->delete();
        Records::select(EventRouteStat::getTable())->delete();
    }

    protected function getAppEventHandlers(): array
    {
        return [
            Core::EVENT_APP_END => new CollectEventHistory
        ];
    }
}