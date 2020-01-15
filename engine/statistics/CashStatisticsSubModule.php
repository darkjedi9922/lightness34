<?php namespace engine\statistics;

use frame\Core;
use frame\database\Records;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\macros\cash\CollectCashRouteStat;
use engine\statistics\macros\cash\EndCollectCashStats;
use engine\statistics\macros\cash\CollectCashCalls;
use frame\tools\Cash;
use engine\statistics\macros\cash\CollectCashError;

class CashStatisticsSubModule extends BaseStatisticsSubModule
{
    public function clearStats()
    {
        Records::from(CashRouteStat::getTable())->delete();
    }

    public function getAppEventHandlers(): array
    {
        $callsCollector = new CollectCashCalls;
        $errorCollector = new CollectCashError($callsCollector);
        $routeCollector = new CollectCashRouteStat;
        $endCollector = new EndCollectCashStats($routeCollector, $callsCollector);

        return [
            Cash::EVENT_CALL => $callsCollector,
            Core::EVENT_APP_ERROR => $errorCollector,
            Core::EVENT_APP_START => $routeCollector,
            Core::EVENT_APP_END => $endCollector
        ];
    }
}