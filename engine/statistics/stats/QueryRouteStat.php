<?php namespace engine\statistics\stats;

use frame\database\Identity;

class QueryRouteStat extends Identity
{
    public static function getTable(): string
    {
        return 'stat_query_routes';
    }
}