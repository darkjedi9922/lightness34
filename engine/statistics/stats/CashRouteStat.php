<?php namespace engine\statistics\stats;

use frame\database\Identity;

class CashRouteStat extends Identity
{
    public static function getTable(): string
    {
        return 'stat_cash_routes';
    }
} 