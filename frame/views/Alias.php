<?php namespace frame\views;

class Alias extends View
{
    public static function resolveAlias(string $alias): ?string
    {
        $view = static::find($alias);
        if (!$view) return null;
        $alias = new static($alias);
        return $alias->getHtml();
    }

    public static function getExtensions(): array
    {
        return ['php'];
    }

    public static function getNamespace(): string
    {
        return 'aliases';
    }
}