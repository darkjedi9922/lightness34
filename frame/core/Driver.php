<?php namespace frame\core;

abstract class Driver
{
    /** @return static */
    public static function getDriver()
    {
        return Core::$app->getDriver(static::class);
    }
}