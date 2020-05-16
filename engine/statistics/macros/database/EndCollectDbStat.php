<?php namespace engine\statistics\macros\database;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\QueryStat;
use engine\statistics\stats\RouteStat;
use frame\database\Records;

class EndCollectDbStat extends BaseStatCollector
{
    private $routeStat;
    private $startQueryCollector;

    public function __construct(
        RouteStat $routeStat,
        StartCollectQueryStat $startQueryCollector
    ) {
        $this->routeStat = $routeStat;
        $this->startQueryCollector = $startQueryCollector;
    }

    protected function collect(...$args)
    {
        $this->insertQueryStats($this->routeStat->getId());
    }

    private function insertQueryStats(int $routeId)
    {
        $sumLoad = 0;
        $hasErrors = false;
        $queryStats = $this->startQueryCollector->getQueryStats();
        $statCount = count($queryStats);

        foreach ($queryStats as $queryStat) {
            /** @var QueryStat $queryStat */
            $queryStat->route_id = $routeId;
            $queryStat->insert();
            $sumLoad += $queryStat->duration_sec;
            if ($queryStat->error !== null) $hasErrors = true;
        }


        Records::from('stat_query_counts')->insert([
            'route_id' => $this->routeStat->getId(),
            'query_count' => $statCount,
            'sum_load' => $sumLoad,
            'status' => !$statCount ? 0 : (!$hasErrors ? 1 : 2)
        ]);
    }
}