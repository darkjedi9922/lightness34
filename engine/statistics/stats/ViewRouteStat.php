<?php namespace engine\statistics\stats;

class ViewRouteStat extends BaseRouteStat
{
    public static function getTable(): string
    {
        return 'stat_view_routes';
    }
}