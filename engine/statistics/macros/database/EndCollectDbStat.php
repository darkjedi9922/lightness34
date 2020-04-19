<?php namespace engine\statistics\macros\database;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\QueryStat;
use engine\statistics\stats\BaseRouteStat;

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
    }

    private function insertQueryStats(int $routeId)
    {
        foreach ($this->startQueryCollector->getQueryStats() as $queryStat) {
            /** @var QueryStat $queryStat */
            $queryStat->route_id = $routeId;
            $queryStat->insert();
        }
    }
}