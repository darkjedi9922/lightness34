<?php namespace engine\statistics;

use frame\database\Records;
use frame\database\Database;
use engine\statistics\stats\QueryStat;
use engine\statistics\macros\database\EndCollectDbStat;
use engine\statistics\macros\database\StartCollectQueryStat;
use engine\statistics\macros\database\EndCollectQueryStat;
use engine\statistics\macros\database\CollectDbError;
use frame\modules\Module;
use frame\errors\Errors;
use engine\statistics\stats\RouteStat;

class DbStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeStat;
    private $startQueryCollector;

    public function __construct(
        string $name,
        RouteStat $routeStat,
        ?Module $parent = null
    ) {
        parent::__construct($name, $parent);
        $this->routeStat = $routeStat;
        $this->startQueryCollector = new StartCollectQueryStat;
    }

    public function clearStats()
    {
        Records::from(QueryStat::getTable())->delete();
    }

    public function endCollecting()
    {
        (new EndCollectDbStat(
            $this->routeStat,
            $this->startQueryCollector
        ))->exec();
    }

    public function getAppEventHandlers(): array
    {
        $endQueryCollector = new EndCollectQueryStat($this->startQueryCollector);
        $errorCollector = new CollectDbError($this->startQueryCollector);

        return [
            Errors::EVENT_ERROR => $errorCollector,
            Database::EVENT_QUERY_START => $this->startQueryCollector,
            Database::EVENT_QUERY_END => $endQueryCollector
        ];
    }
}