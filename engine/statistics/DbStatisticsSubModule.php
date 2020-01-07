<?php namespace engine\statistics;

use frame\Core;
use frame\database\Records;
use frame\database\Database;
use engine\statistics\stats\QueryRouteStat;
use engine\statistics\stats\QueryStat;
use engine\statistics\macros\database\CollectQueryRouteStat;
use engine\statistics\macros\database\EndCollectDbStat;
use engine\statistics\macros\database\StartCollectQueryStat;
use engine\statistics\macros\database\EndCollectQueryStat;

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
        $startQueryCollector = new StartCollectQueryStat;
        $endQueryCollector = new EndCollectQueryStat($startQueryCollector);
        $endCollector = new EndCollectDbStat(
            $routeStatCollector,
            $startQueryCollector
        );

        return [
            Core::EVENT_APP_START => $routeStatCollector,
            Database::EVENT_QUERY_START => $startQueryCollector,
            Database::EVENT_QUERY_END => $endQueryCollector,
            Core::EVENT_APP_END => $endCollector
        ];
    }
}