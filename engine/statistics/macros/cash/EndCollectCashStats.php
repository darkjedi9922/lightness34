<?php namespace engine\statistics\macros\cash;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\stats\CashValueStat;
use engine\statistics\stats\BaseRouteStat;
use frame\stdlib\cash\database;
use frame\stdlib\cash\config;

class EndCollectCashStats extends BaseStatCollector
{
    private $routeStat;
    private $valuesCollector;

    public function __construct(
        BaseRouteStat $routeStat,
        CollectCashCalls $valuesCollector
    ) {
        $this->routeStat = $routeStat;
        $this->valuesCollector = $valuesCollector;
    }

    protected function collect(...$args)
    {
        $routeId = $this->routeStat->insert();
        $this->insertValueStats($routeId);
        $this->deleteOldStats();
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

    private function deleteOldStats()
    {
        $routeTable = CashRouteStat::getTable();
        $cashTable = CashValueStat::getTable();
        $time = time() - config::get('statistics')->storeTimeInSeconds;
        database::get()->query(
            "DELETE $routeTable
            FROM $routeTable LEFT JOIN $cashTable
                ON $routeTable.id = $cashTable.route_id
            WHERE $routeTable.time < $time"
        );
    }
}