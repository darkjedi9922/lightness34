<?php namespace engine\statistics;

use frame\core\Core;
use frame\database\Records;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\macros\cash\EndCollectCashStats;
use engine\statistics\macros\cash\CollectCashCalls;
use frame\tools\Cash;
use engine\statistics\macros\cash\CollectCashError;
use frame\modules\Module;

class CashStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeStat;
    private $callsCollector;

    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);
        $this->routeStat = new CashRouteStat;
        $this->callsCollector = new CollectCashCalls;
    }

    public function clearStats()
    {
        Records::from(CashRouteStat::getTable())->delete();
    }

    public function endCollecting()
    {
        (new EndCollectCashStats(
            $this->routeStat,
            $this->callsCollector)
        )->exec();
    }

    public function getAppEventHandlers(): array
    {
        $this->routeStat->collectCurrent();
        $errorCollector = new CollectCashError($this->callsCollector);

        return [
            Cash::EVENT_CALL => $this->callsCollector,
            Core::EVENT_APP_ERROR => $errorCollector
        ];
    }
}