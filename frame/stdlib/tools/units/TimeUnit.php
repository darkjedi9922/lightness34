<?php namespace frame\stdlib\tools\units;

class TimeUnit extends \frame\tools\Unit
{
    const SECONDS = 'seconds';
    const MINUTES = 'minutes';
    const HOURS = 'hours';
    const DAYS = 'days';
    const MONTHS = 'months';
    const YEARS = 'years';

    public static function getOrderedUnits(): array
    {
        return [
            self::SECONDS   => 1,
            self::MINUTES   => 1*60,
            self::HOURS     => 1*60*60,
            self::DAYS      => 1*60*60*24,
            self::MONTHS    => 1*60*60*24*30,
            self::YEARS     => 1*60*60*24*30*12
        ];
    }
}