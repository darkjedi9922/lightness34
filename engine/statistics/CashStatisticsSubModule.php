<?php namespace engine\statistics;

use frame\Core;
use frame\database\Records;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\macros\cash\CollectCashRouteStat;
use engine\statistics\macros\cash\EndCollectCashStats;
use engine\statistics\macros\cash\CollectCashCalls;
use frame\tools\Cash;
use engine\statistics\macros\cash\CollectCashError;
use frame\modules\Module;

class CashStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeCollector;
    private $callsCollector;

    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);
        $this->routeCollector = new CollectCashRouteStat;
        $this->callsCollector = new CollectCashCalls;
    }

    public function clearStats()
    {
        Records::from(CashRouteStat::getTable())->delete();
    }

    public function endCollecting()
    {
        (new EndCollectCashStats(
            $this->routeCollector,
            $this->callsCollector)
        )->exec();
    }

    public function getAppEventHandlers(): array
    {
        
        $errorCollector = new CollectCashError($this->callsCollector);

        return [
            Cash::EVENT_CALL => $this->callsCollector,
            Core::EVENT_APP_ERROR => $errorCollector,
            Core::EVENT_APP_START => $this->routeCollector
        ];
    }
}