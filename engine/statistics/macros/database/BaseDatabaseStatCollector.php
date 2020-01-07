<?php namespace engine\statistics\macros\database;

use engine\statistics\macros\BaseStatCollector;

abstract class BaseDatabaseStatCollector extends BaseStatCollector
{
    protected function isSqlAboutStats(string $sql): bool
    {
        return strpos($sql, 'stat_') !== false;
    }

    protected function collect(...$args)
    {
        $this->collectDb(...$args);
    }

    protected abstract function collectDb(...$args);
}