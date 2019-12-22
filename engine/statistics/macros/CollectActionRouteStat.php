<?php namespace engine\statistics\macros;

use engine\statistics\stats\RouteStat;

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