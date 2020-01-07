<?php namespace engine\statistics\macros\database;

use engine\statistics\macros\BaseStatCollector;

class EndCollectQueryStat extends BaseStatCollector
{
    private $startQueryCollector;

    public function __construct(StartCollectQueryStat $startQueryCollector)
    {
        $this->startQueryCollector = $startQueryCollector;
    }

    protected function collect(...$args)
    {
        $this->startQueryCollector->measureLastQueryDuration();
    }
}