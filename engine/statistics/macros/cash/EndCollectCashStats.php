<?php namespace engine\statistics\macros\cash;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\CashValueStat;
use engine\statistics\stats\RouteStat;
use frame\database\Records;

class EndCollectCashStats extends BaseStatCollector
{
    private $routeStat;
    private $valuesCollector;

    public function __construct(
        RouteStat $routeStat,
        CollectCashCalls $valuesCollector
    ) {
        $this->routeStat = $routeStat;
        $this->valuesCollector = $valuesCollector;
    }

    protected function collect(...$args)
    {
        $this->insertValueStats($this->routeStat->getId());
    }

    private function insertValueStats(int $routeId)
    {
        $stats = $this->valuesCollector->getValueStats();
        $valueCount = 0;
        $callCount = 0;
        $hasErrors = false;

        foreach ($stats as $class => $keyStats) {
            foreach ($keyStats as $key => $stat) {
                /** @var CashValueStat $stat */
                $stat->route_id = $routeId;
                $stat->insert();
                $valueCount += 1;
                $callCount += $stat->call_count;
                if ($stat->init_error !== null) $hasErrors = true;
            }
        }

        Records::from('stat_cash_counts')->insert([
            'route_id' => $this->routeStat->getId(),
            'value_count' => $valueCount,
            'call_count' => $callCount,
            'status' => !$callCount ? 0 : (!$hasErrors ? 1 : 2)
        ]);
    }
}