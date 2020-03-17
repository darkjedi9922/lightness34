<?php namespace frame\core;

abstract class Driver
{
    /** @return static */
    public static function get()
    {
        return Core::$app->getDriver(static::class);
    }
}