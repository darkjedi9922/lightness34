<?php namespace engine\statistics\macros;

use engine\statistics\stats\RouteStat;
use frame\route\Request;
use frame\Core;
use engine\statistics\stats\TimeStat;

class StartCollectRouteStat extends BaseStatCollector
{
    private $stat;
    private $timer;

    public function __construct(RouteStat $stat, TimeStat $timer)
    {
        $this->stat = $stat;
        $this->timer = $timer;
    }

    protected function collect(...$args)
    {
        $this->timer->start();
        $router = Core::$app->router;
        $this->stat->url = $router->url;
        $this->stat->type = RouteStat::ROUTE_TYPE_PAGE;
        $this->stat->ajax = Request::isAjax();
        $this->stat->time = time();
    }
}