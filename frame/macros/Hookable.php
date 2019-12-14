<?php namespace frame\macros;

use frame\macros\Macro;

abstract class Hookable
{
    private static $macros = [];

    public static final function addHook(string $hook, Macro $macro)
    {
        self::$macros[static::class][$hook][] = $macro;
    }

    protected static final function hook(string $hook)
    {
        foreach (self::$macros[static::class][$hook] ?? [] as $macro) {
            /** @var Macro $macro */
            $macro->exec();
        }
    }
}