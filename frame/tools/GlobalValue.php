<?php namespace frame\tools;

abstract class GlobalValue
{
    private static $storage = [];

    public static function get()
    {
        return self::$storage[static::class] ?? 
            self::$storage[static::class] = static::create();
    }

    public abstract static function create();
}