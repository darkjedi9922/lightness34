<?php namespace frame\route;

use frame\events\Macro;
use frame\route\Router;

abstract class GetMacro extends Macro
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function exec(...$args)
    {
        $value = Router::getDriver()->getCurrentRoute()->getArg($this->name);
        if ($value !== null) $this->triggerExec($value);
    }

    protected abstract function triggerExec(string $value);
}