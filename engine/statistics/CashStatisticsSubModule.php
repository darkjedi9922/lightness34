<?php namespace engine\statistics;

use frame\database\Records;
use engine\statistics\macros\cash\EndCollectCashStats;
use engine\statistics\macros\cash\CollectCashCalls;
use engine\statistics\macros\cash\CollectCashError;
use frame\modules\Module;
use frame\errors\Errors;
use engine\statistics\stats\RouteStat;
use engine\statistics\stats\CashValueStat;
use frame\core\Core;
use frame\cash\StaticCashStorage;
use engine\statistics\tools\StatCashStorage;

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
        Core::$app->decorateDriver(StaticCashStorage::class, StatCashStorage::class);
    }

    public function clearStats()
    {
        Records::from(CashValueStat::getTable())->delete();
        Records::from('stat_cash_counts')->delete();
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
            StatCashStorage::EVENT_CASH_CALL => $this->callsCollector,
            Errors::EVENT_ERROR => $errorCollector
        ];
    }
}