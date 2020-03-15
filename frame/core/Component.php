<?php namespace frame\core;

abstract class Component
{
    /** @return static */
    public static function get()
    {
        return Core::$app->getComponent(static::class);
    }
}