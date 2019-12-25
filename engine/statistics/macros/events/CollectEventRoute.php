<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventRouteStat;
use frame\Core;

class CollectEventRoute extends BaseStatCollector
{
    private $routeStat;

    public function __construct(EventRouteStat $routeStat)
    {
        $this->routeStat = $routeStat;
    }

    protected function collect(...$args)
    {
        $router = Core::$app->router;
        $this->routeStat->route = $router->pagename;
    }
}