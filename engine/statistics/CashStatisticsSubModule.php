<?php namespace engine\statistics;

use frame\Core;
use frame\database\Records;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\macros\cash\CollectCashRouteStat;
use engine\statistics\macros\cash\EndCollectCashStats;
use engine\statistics\macros\cash\CollectCashValues;
use frame\tools\Cash;

class CashStatisticsSubModule extends BaseStatisticsSubModule
{
    public function clearStats()
    {
        Records::from(CashRouteStat::getTable())->delete();
    }

    protected function getAppEventHandlers(): array
    {
        $valuesCollector = new CollectCashValues;
        $routeCollector = new CollectCashRouteStat;
        $endCollector = new EndCollectCashStats($routeCollector, $valuesCollector);

        return [
            Cash::EVENT_CALL => $valuesCollector,
            Core::EVENT_APP_START => $routeCollector,
            Core::EVENT_APP_END => $endCollector
        ];
    }
}