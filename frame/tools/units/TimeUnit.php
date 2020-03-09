<?php namespace frame\tools\units;

class TimeUnit extends Unit
{
    const SECONDS = 1;
    const MINUTES = self::SECONDS * 60;
    const HOURS = self::MINUTES * 60;
    const DAYS = self::HOURS * 24;
    const MONTHS = self::DAYS * 30;
    const YEARS = self::MONTHS * 12;

    public static function getOrderedUnits(): array
    {
        return [
            self::SECONDS, self::MINUTES, self::HOURS,
            self::DAYS, self::MONTHS, self::YEARS
        ];
    }
}