<?php namespace engine\statistics\macros\views;

use engine\statistics\macros\BaseStatCollector;

class EndCollectViewStats extends BaseStatCollector
{
    private $startCollector;

    public function __construct(StartCollectViewStats $startCollector)
    {
        $this->startCollector = $startCollector;
    }

    protected function collect(...$args)
    {
        $this->startCollector->endViewStatCollecting($args[0]);
    }
}