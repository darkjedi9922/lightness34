<?php namespace engine\statistics\macros\routes;

use frame\route\Request;
use engine\statistics\stats\TimeStat;
use engine\statistics\stats\RouteStat;
use engine\statistics\macros\BaseStatCollector;
use frame\stdlib\cash\router;

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
        $this->stat->url = router::get()->url;
        $this->stat->type = RouteStat::ROUTE_TYPE_PAGE;
        $this->stat->ajax = Request::getDriver()->isAjax();
        $this->stat->time = time();
    }
}