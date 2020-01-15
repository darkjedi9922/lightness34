<?php namespace engine\statistics\macros\database;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\QueryStat;
use engine\statistics\stats\QueryRouteStat;
use engine\statistics\stats\BaseRouteStat;
use frame\cash\database;
use frame\cash\config;

class EndCollectDbStat extends BaseStatCollector
{
    private $routeStat;
    private $startQueryCollector;

    public function __construct(
        BaseRouteStat $routeStat,
        StartCollectQueryStat $startQueryCollector
    ) {
        $this->routeStat = $routeStat;
        $this->startQueryCollector = $startQueryCollector;
    }

    protected function collect(...$args)
    {
        $routeId = $this->routeStat->insert();
        $this->insertQueryStats($routeId);
        $this->deleteOldStats();
    }

    private function insertQueryStats(int $routeId)
    {
        foreach ($this->startQueryCollector->getQueryStats() as $queryStat) {
            /** @var QueryStat $queryStat */
            $queryStat->route_id = $routeId;
            $queryStat->insert();
        }
    }

    private function deleteOldStats()
    {
        $routeTable = QueryRouteStat::getTable();
        $queryTable = QueryStat::getTable();
        $limit = config::get('statistics')->{'queries.history.limit'};
        database::get()->query(
            "DELETE $routeTable, $queryTable
            FROM
                $routeTable 
                LEFT OUTER JOIN $queryTable 
                    ON $routeTable.id = $queryTable.route_id
                INNER JOIN
                (
                    SELECT id FROM $routeTable 
                    ORDER BY id DESC LIMIT $limit, 999999
                ) AS cond_table
                    ON $routeTable.id = cond_table.id"
        );
    }
}