<?php namespace engine\statistics\stats;

class QueryRouteStat extends BaseRouteStat
{
    public static function getTable(): string
    {
        return 'stat_query_routes';
    }
}