<?php namespace engine\statistics\macros\cash;

use engine\statistics\macros\BaseStatCollector;
use frame\cash\database;
use engine\statistics\stats\CashRouteStat;
use frame\cash\config;

class EndCollectCashStats extends BaseStatCollector
{
    private $routeCollector;

    public function __construct(CollectCashRouteStat $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    protected function collect(...$args)
    {
        $this->routeCollector->getRouteStat()->insert();
        $this->deleteOldStats();
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