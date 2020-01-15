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
use engine\statistics\macros\database\CollectDbError;
use frame\modules\Module;

class DbStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeStatCollector;
    private $startQueryCollector;

    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);
        $this->routeStatCollector = new CollectQueryRouteStat;
        $this->startQueryCollector = new StartCollectQueryStat;
    }

    public function clearStats()
    {
        Records::from(QueryStat::getTable())->delete();
        Records::from(QueryRouteStat::getTable())->delete();
    }

    public function endCollecting()
    {
        (new EndCollectDbStat(
            $this->routeStatCollector,
            $this->startQueryCollector
        ))->exec();
    }

    public function getAppEventHandlers(): array
    {
        $endQueryCollector = new EndCollectQueryStat($this->startQueryCollector);
        $errorCollector = new CollectDbError($this->startQueryCollector);

        return [
            Core::EVENT_APP_START => $this->routeStatCollector,
            Core::EVENT_APP_ERROR => $errorCollector,
            Database::EVENT_QUERY_START => $this->startQueryCollector,
            Database::EVENT_QUERY_END => $endQueryCollector
        ];
    }
}