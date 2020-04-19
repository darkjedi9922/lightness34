<?php namespace engine\statistics\macros\cash;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\CashValueStat;
use engine\statistics\stats\RouteStat;

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
        foreach ($stats as $class => $keyStats) {
            foreach ($keyStats as $key => $stat) {
                /** @var CashValueStat $stat */
                $stat->route_id = $routeId;
                $stat->insert();
            }
        }
    }
}