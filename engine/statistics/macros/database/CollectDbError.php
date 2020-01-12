<?php namespace engine\statistics\macros\database;

use engine\statistics\macros\BaseStatCollector;
use frame\database\QueryException;

class CollectDbError extends BaseStatCollector
{
    private $startQueryCollector;

    public function __construct(StartCollectQueryStat $startQueryCollector)
    {
        $this->startQueryCollector = $startQueryCollector;
    }

    protected function collect(...$args)
    {
        if ($this->startQueryCollector->isLastQueryIgnored()) return;

        /** @var \Throwable $error */
        $error = $args[0];

        if (!($error instanceof QueryException)) return;
        
        $queryStat = $this->startQueryCollector->getLastNonIgnoredQueryStat();
        $queryStat->error = $error->getCode() . ': ' . $error->getMessage();
        $queryStat->error = str_replace('\\', '\\\\', $queryStat->error);
    }
}