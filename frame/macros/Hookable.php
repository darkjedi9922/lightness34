<?php namespace frame\macros;

use frame\macros\Macro2;

abstract class Hookable
{
    private static $macros = [];

    public static final function addHook(string $hook, Macro2 $macro)
    {
        self::$macros[static::class][$hook][] = $macro;
    }

    protected static final function hook(string $hook)
    {
        foreach (self::$macros[static::class][$hook] ?? [] as $macro) {
            /** @var Macro2 $macro */
            $macro->exec();
        }
    }
}