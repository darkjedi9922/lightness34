<?php namespace engine\statistics\macros\database;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\QueryStat;

class EndCollectDbStat extends BaseStatCollector
{
    private $routeStatCollector;
    private $startQueryCollector;

    public function __construct(
        CollectQueryRouteStat $routeStatCollector,
        StartCollectQueryStat $startQueryCollector
    ) {
        $this->routeStatCollector = $routeStatCollector;
        $this->startQueryCollector = $startQueryCollector;
    }

    protected function collect(...$args)
    {
        $routeId = $this->routeStatCollector->getRouteStat()->insert();
        foreach ($this->startQueryCollector->getQueryStats() as $queryStat) {
            /** @var QueryStat $queryStat */
            $queryStat->route_id = $routeId;
            $queryStat->insert();
        }
    }
}