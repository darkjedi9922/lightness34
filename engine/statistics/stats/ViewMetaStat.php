<?php namespace engine\statistics\stats;

use frame\database\Identity;

class ViewMetaStat extends Identity
{
    public static function getTable(): string
    {
        return 'stat_view_meta';
    }
}