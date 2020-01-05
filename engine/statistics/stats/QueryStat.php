<?php namespace engine\statistics\stats;

use frame\database\Identity;

class QueryStat extends Identity
{
    public static function getTable(): string
    {
        return 'stat_queries';
    }
}