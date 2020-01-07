<?php namespace engine\statistics\macros\database;

class EndCollectQueryStat extends BaseDatabaseStatCollector
{
    private $startQueryCollector;

    public function __construct(StartCollectQueryStat $startQueryCollector)
    {
        $this->startQueryCollector = $startQueryCollector;
    }

    protected function collectDb(...$args)
    {
        $sql = $args[0];
        if ($this->isSqlAboutStats($sql)) return;
        $this->startQueryCollector->measureLastQueryDuration();
    }
}