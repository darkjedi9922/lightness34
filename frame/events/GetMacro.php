<?php namespace frame\events;

use frame\cash\router;

abstract class GetMacro extends Macro
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function exec(...$args)
    {
        $value = router::get()->getArg($this->name);
        if ($value !== null) $this->triggerExec($value);
    }

    protected abstract function triggerExec(string $value);
}