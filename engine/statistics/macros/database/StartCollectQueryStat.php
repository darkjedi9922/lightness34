<?php namespace engine\statistics\macros\database;

use engine\statistics\stats\QueryStat;
use engine\statistics\stats\TimeStat;

class StartCollectQueryStat extends BaseDatabaseStatCollector
{
    private $queryStats = [];
    /** @var TimeStat */
    private $lastNonIgnoredQueryTimer = null;
    private $lastQueryIgnored = false;

    public function getQueryStats(): array
    {
        return $this->queryStats;
    }

    public function isLastQueryIgnored(): bool
    {
        return $this->lastQueryIgnored;
    }

    public function getLastNonIgnoredQueryStat(): ?QueryStat
    {
        if (empty($this->queryStats)) return null;
        return $this->queryStats[array_key_last($this->queryStats)];
    }

    public function measureLastNonIgnoredQueryDuration()
    {
        $this->queryStats[array_key_last($this->queryStats)]
            ->duration_sec = $this->lastNonIgnoredQueryTimer->resultInSeconds();
    }

    protected function collectDb(...$args)
    {
        $sql = $args[0];

        if ($this->isSqlAboutStats($sql)) {
            $this->lastQueryIgnored = true;
            return;
        } else {
            $this->lastQueryIgnored = false;
        };

        $queryStat = new QueryStat;
        $queryStat->sql_text = $sql;

        $this->queryStats[] = $queryStat;
        
        $this->lastNonIgnoredQueryTimer = new TimeStat;
        $this->lastNonIgnoredQueryTimer->start();
    }
}