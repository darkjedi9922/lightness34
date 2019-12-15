<?php namespace engine\statistics\macros;

use frame\Core;
use frame\macros\Macro;
use engine\statistics\RouteStat;

class RouteStatMacro implements Macro
{
    private $stat;

    public function __construct(RouteStat $stat)
    {
        $this->stat = $stat;
    }

    public function exec()
    {
        $router = Core::$app->router;
        $this->stat->setUrl($router->url);
        $this->stat->setRoute($router->pagename);
        $this->stat->setParams($router->args);
    }
}