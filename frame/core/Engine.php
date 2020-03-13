<?php namespace frame\core;

class Engine
{
    /** @return static */
    public static function get()
    {
        return Core::$app->getUseInstance(static::class);
    }
}