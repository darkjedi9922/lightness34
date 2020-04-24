<?php namespace engine\statistics;

use frame\database\Records;
use engine\statistics\macros\cash\EndCollectCashStats;
use engine\statistics\macros\cash\CollectCashCalls;
use engine\statistics\macros\cash\CollectCashError;
use frame\modules\Module;
use frame\errors\Errors;
use frame\cash\CashValue;
use engine\statistics\stats\RouteStat;
use engine\statistics\stats\CashValueStat;

class CashStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeStat;
    private $callsCollector;

    public function __construct(
        string $name,
        RouteStat $routeStat,
        ?Module $parent = null
    ) {
        parent::__construct($name, $parent);
        $this->routeStat = $routeStat;
        $this->callsCollector = new CollectCashCalls;
    }

    public function clearStats()
    {
        Records::from(CashValueStat::getTable())->delete();
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
        $errorCollector = new CollectCashError($this->callsCollector);

        return [
            CashValue::EVENT_CALL => $this->callsCollector,
            Errors::EVENT_ERROR => $errorCollector
        ];
    }
}