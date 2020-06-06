<?php namespace frame\console\views;

use frame\views\DynamicPage;

class ConsoleDynamicPage extends DynamicPage
{
    public static function getNamespace(): string
    {
        return 'console';
    }
}