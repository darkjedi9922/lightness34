<?php namespace engine\statistics\stats;

use frame\database\Identity;

class ViewStat extends Identity
{
    public static function getTable(): string
    {
        return 'stat_views';
    }
}