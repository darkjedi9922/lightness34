<?php namespace engine\statistics\macros;

use frame\macros\Macro;
use engine\statistics\stats\TimeStat;

abstract class BaseStatCollector extends Macro
{
    public function exec(...$args)
    {
        TimeStat::pauseAll();
        $this->collect(...$args);
        TimeStat::resumeAll();
    }

    protected abstract function collect(...$args);
}