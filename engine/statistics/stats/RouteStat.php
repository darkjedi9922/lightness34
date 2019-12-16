<?php namespace engine\statistics\stats;

use frame\database\Identity;

class RouteStat extends Identity
{
    const ROUTE_TYPE_PAGE = 1;
    const ROUTE_TYPE_ACTION = 2;

    public static function getTable(): string
    {
        return 'stat_routes';
    }
}