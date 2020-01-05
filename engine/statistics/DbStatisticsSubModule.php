<?php namespace engine\statistics;

use frame\database\Records;
use engine\statistics\stats\QueryRouteStat;
use engine\statistics\stats\QueryStat;

class DbStatisticsSubModule extends BaseStatisticsSubModule
{
    public function clearStats()
    {
        Records::from(QueryStat::getTable())->delete();
        Records::from(QueryRouteStat::getTable())->delete();
    }

    protected function getAppEventHandlers(): array
    {
        return [];
    }
}