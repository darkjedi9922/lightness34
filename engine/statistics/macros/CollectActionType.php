<?php namespace engine\statistics\macros;

use frame\macros\Macro;
use engine\statistics\stats\RouteStat;

class CollectActionType implements Macro
{
    private $stat;

    public function __construct(RouteStat $stat)
    {
        $this->stat = $stat;
    }

    public function exec(...$args)
    {
        $this->stat->type = RouteStat::ROUTE_TYPE_ACTION;
    }
}