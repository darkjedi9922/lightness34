<?php namespace frame\console\views;

use frame\views\Page;

class ConsolePage extends Page
{
    public static function getNamespace(): string
    {
        return 'console';
    }
}