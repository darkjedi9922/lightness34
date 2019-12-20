<?php namespace engine\statistics\stats;

use frame\database\Identity;

class DynamicRouteParam extends Identity
{
    public static function getTable(): string
    {
        return 'stat_dynamic_route_params';
    }
}