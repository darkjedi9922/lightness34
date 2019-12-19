<?php namespace engine\statistics\macros;

use frame\macros\Macro;
use engine\statistics\stats\RouteStat;
use frame\route\Response;

class CollectRouteEndStat implements Macro
{
    private $stat;

    public function __construct(RouteStat $stat)
    {
        $this->stat = $stat;
    }

    public function exec(...$args)
    {
        $this->stat->code = Response::getCode();
    }
}