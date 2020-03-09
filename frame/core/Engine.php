<?php namespace frame\core;

class Engine
{
    private static $instances = [];

    /** @return static */
    public static function get()
    {
        if (!isset(self::$instances[static::class]))
            if (($class = Core::$app->getUse(static::class)) !== null)
                return $class::get();
            else self::$instances[static::class] = new static;
        return self::$instances[static::class];
    }
}