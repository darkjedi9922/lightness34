<?php namespace engine\statistics\macros;

use engine\statistics\stats\RouteStat;
use frame\route\Request;
use frame\macros\Macro;
use frame\Core;

class StartCollectRouteStat extends Macro
{
    private $stat;

    public function __construct(RouteStat $stat)
    {
        $this->stat = $stat;
    }

    public function exec(...$args)
    {
        $router = Core::$app->router;
        $this->stat->url = $router->url;
        $this->stat->type = RouteStat::ROUTE_TYPE_PAGE;
        $this->stat->ajax = Request::isAjax();
        $this->stat->time = time();
    }
}