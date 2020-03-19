<?php namespace frame\errors\handlers;

class ErrorPage extends \frame\views\Page
{
    public static function getNamespace(): string
    {
        return 'errors';
    }
}