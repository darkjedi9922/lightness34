<?php namespace frame\tools\units;

class ByteUnit extends Unit
{
    const BYTES = 'bytes';
    const KB = 'KB';
    const MB = 'MB';
    const GB = 'GB';
    const TB = 'TB';
    const PB = 'PB';

    public static function getOrderedUnits(): array
    {
        return [
            self::BYTES => 1,
            self::KB    => 1*1024,
            self::MB    => 1*1024*1024,
            self::GB    => 1*1024*1024*1024,
            self::TB    => 1*1024*1024*1024*1024,
            self::PB    => 1*1024*1024*1024*1024*1024
        ];
    }
}