<?php namespace frame\tools;

/**
 * Не используй этот механизм во frame и engine классах и тогда классы будет проще 
 * тестировать, и их архитектура будет лучше.
 */
abstract class Cash
{
    public abstract static function get();

    protected static function cash(callable $creator)
    {
        return self::$storage[static::class] ?? 
            self::$storage[static::class] = $creator();
    }
    
    private static $storage = [];
}