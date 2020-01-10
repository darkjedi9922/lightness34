<?php namespace engine\statistics\macros\cash;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\CashRouteStat;
use frame\Core;

class CollectCashRouteStat extends BaseStatCollector
{
    private $routeStat;

    public function getRouteStat(): CashRouteStat
    {
        return $this->routeStat;
    }

    protected function collect(...$args)
    {
        $this->routeStat = new CashRouteStat;
        $this->routeStat->route = Core::$app->router->pagename;
        $this->routeStat->time = time();
    }
}