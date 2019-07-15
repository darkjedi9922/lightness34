<?php namespace frame\tools;

/**
 * Не используй этот механизм во frame и engine классах и тогда классы будет проще 
 * тестировать, и их архитектура будет лучше.
 */
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