<?php namespace frame\route;

use frame\events\Macro;
use frame\route\Router;

abstract class RouteNamespaceMacro extends Macro
{
    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function exec(...$args)
    {
        $router = Router::getDriver()->getCurrentRoute();
        if ($router->isInNamespace($this->namespace)) $this->run();
    }

    protected abstract function run();
}