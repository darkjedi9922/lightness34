<?php namespace engine\statistics;

use frame\Core;
use frame\database\Records;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\macros\cash\CollectCashRouteStat;
use engine\statistics\macros\cash\EndCollectCashStats;

class CashStatisticsSubModule extends BaseStatisticsSubModule
{
    public function clearStats()
    {
        Records::from(CashRouteStat::getTable())->delete();
    }

    protected function getAppEventHandlers(): array
    {
        $routeCollector = new CollectCashRouteStat;
        $endCollector = new EndCollectCashStats($routeCollector);

        return [
            Core::EVENT_APP_START => $routeCollector,
            Core::EVENT_APP_END => $endCollector
        ];
    }
}