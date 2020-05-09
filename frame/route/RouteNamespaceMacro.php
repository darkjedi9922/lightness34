<?php namespace frame\route;

use frame\events\Macro;
use frame\stdlib\cash\router;

abstract class RouteNamespaceMacro extends Macro
{
    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function exec(...$args)
    {
        $router = router::get();
        if ($router->isInNamespace($this->namespace)) $this->run();
    }

    protected abstract function run();
}