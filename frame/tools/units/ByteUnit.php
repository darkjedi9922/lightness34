<?php namespace frame\tools\units;

class ByteUnit extends Unit
{
    const BYTES = 1;
    const KB = self::BYTES * 1024;
    const MB = self::KB * 1024;
    const GB = self::MB * 1024;
    const TB = self::GB * 1024;
    const PB = self::TB * 1024;

    public static function getOrderedUnits(): array
    {
        return [self::BYTES, self::KB, self::MB, self::GB, self::TB, self::PB];
    }
}