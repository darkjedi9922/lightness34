<?php namespace frame\macros;

use frame\Core;

abstract class GetMacro extends Macro
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function exec(...$args)
    {
        $value = Core::$app->router->getArg($this->name);
        if ($value !== null) $this->triggerExec($value);
    }

    protected abstract function triggerExec(string $value);
}