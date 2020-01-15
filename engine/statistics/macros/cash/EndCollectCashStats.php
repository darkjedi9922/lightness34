<?php namespace engine\statistics\macros\cash;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\CashRouteStat;
use engine\statistics\stats\CashValueStat;
use frame\cash\database;
use frame\cash\config;

class EndCollectCashStats extends BaseStatCollector
{
    private $routeCollector;
    private $valuesCollector;

    public function __construct(
        CollectCashRouteStat $routeCollector,
        CollectCashCalls $valuesCollector
    ) {
        $this->routeCollector = $routeCollector;
        $this->valuesCollector = $valuesCollector;
    }

    protected function collect(...$args)
    {
        $routeId = $this->routeCollector->getRouteStat()->insert();
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
        $limit = config::get('statistics')->{'cash.history.limit'};
        database::get()->query(
            "DELETE $routeTable
            FROM
                $routeTable
                INNER JOIN
                (
                    SELECT id FROM $routeTable 
                    ORDER BY id DESC LIMIT $limit, 999999
                ) AS cond_table
                    ON $routeTable.id = cond_table.id"
        );
    }
}