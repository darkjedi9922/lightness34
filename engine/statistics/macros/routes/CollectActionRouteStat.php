<?php namespace engine\statistics\macros\routes;

use engine\statistics\stats\RouteStat;
use engine\statistics\macros\BaseStatCollector;

class CollectActionRouteStat extends BaseStatCollector
{
    private $stat;

    public function __construct(RouteStat $stat)
    {
        $this->stat = $stat;    
    }

    protected function collect(...$args)
    {
        $this->stat->type = RouteStat::ROUTE_TYPE_ACTION;
    }
}