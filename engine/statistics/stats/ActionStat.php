<?php namespace engine\statistics\stats;

use frame\database\Identity;

class ActionStat extends Identity
{
    public static function getTable(): string
    {
        return 'stat_actions';
    }
}