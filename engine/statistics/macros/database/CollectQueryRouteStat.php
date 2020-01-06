<?php namespace engine\statistics\macros\database;

use frame\Core;
use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\QueryRouteStat;

class CollectQueryRouteStat extends BaseStatCollector
{
    private $routeStat = null;

    public function getRouteStat(): ?QueryRouteStat
    {
        return $this->routeStat;
    }

    protected function collect(...$args)
    {
        $router = Core::$app->router;
        $this->routeStat = new QueryRouteStat;
        $this->routeStat->route = $router->pagename;
        $this->routeStat->time = time();
    }
} 