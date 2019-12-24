<?php namespace engine\statistics\stats;

use frame\database\Identity;

class ActionStat extends Identity
{
    const RESPONSE_TYPE_JSON = 1;
    const RESPONSE_TYPE_ERROR = 2;
    const RESPONSE_TYPE_REDIRECT = 3;

    public static function getTable(): string
    {
        return 'stat_actions';
    }
}