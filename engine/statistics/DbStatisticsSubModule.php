<?php namespace engine\statistics;

use frame\Core;
use frame\database\Records;
use engine\statistics\stats\QueryRouteStat;
use engine\statistics\stats\QueryStat;
use engine\statistics\macros\database\CollectQueryRouteStat;
use engine\statistics\macros\database\EndCollectDbStat;

class DbStatisticsSubModule extends BaseStatisticsSubModule
{
    public function clearStats()
    {
        Records::from(QueryStat::getTable())->delete();
        Records::from(QueryRouteStat::getTable())->delete();
    }

    protected function getAppEventHandlers(): array
    {
        $routeStatCollector = new CollectQueryRouteStat;
        $endCollector = new EndCollectDbStat($routeStatCollector);

        return [
            Core::EVENT_APP_START => $routeStatCollector,
            Core::EVENT_APP_END => $endCollector
        ];
    }
}