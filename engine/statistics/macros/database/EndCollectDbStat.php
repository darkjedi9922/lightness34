<?php namespace engine\statistics\macros\database;

use engine\statistics\macros\BaseStatCollector;

class EndCollectDbStat extends BaseStatCollector
{
    private $routeStatCollector;

    public function __construct(CollectQueryRouteStat $routeStatCollector)
    {
        $this->routeStatCollector = $routeStatCollector;
    }

    protected function collect(...$args)
    {
        $routeId = $this->routeStatCollector->getRouteStat()->insert();
    }
}