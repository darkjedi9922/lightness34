<?php namespace engine\statistics\stats;

class CashRouteStat extends BaseRouteStat
{
    public static function getTable(): string
    {
        return 'stat_cash_routes';
    }
} 