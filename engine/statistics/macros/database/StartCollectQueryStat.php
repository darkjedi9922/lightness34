<?php namespace engine\statistics\macros\database;

use engine\statistics\stats\QueryStat;
use engine\statistics\stats\TimeStat;

class StartCollectQueryStat extends BaseDatabaseStatCollector
{
    private $queryStats = [];
    /** @var TimeStat */
    private $lastQueryTimer = null;

    public function getQueryStats(): array
    {
        return $this->queryStats;
    }

    public function measureLastQueryDuration()
    {
        $this->queryStats[array_key_last($this->queryStats)]
            ->duration_sec = $this->lastQueryTimer->resultInSeconds();
    }

    protected function collectDb(...$args)
    {
        $sql = $args[0];

        if ($this->isSqlAboutStats($sql)) return;

        $queryStat = new QueryStat;
        $queryStat->sql_text = $sql;

        $this->queryStats[] = $queryStat;
        
        $this->lastQueryTimer = new TimeStat;
        $this->lastQueryTimer->start();
    }
}