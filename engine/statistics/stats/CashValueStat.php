<?php namespace engine\statistics\stats;

use frame\database\Identity;

class CashValueStat extends Identity
{
    public static function getTable(): string
    {
        return 'stat_cash_values';
    }
}