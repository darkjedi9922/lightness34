<?php namespace frame\tools;

abstract class GlobalValue
{
    private static $storage = [];
    private static $globalize = true;

    public static function enableGlobalization(bool $enable)
    {
        self::$globalize = $enable;
    }

    public static function get()
    {
        if (self::$globalize) return self::$storage[static::class] ?? 
            self::$storage[static::class] = static::create();
        else return static::create();
    }

    public abstract static function create();
}