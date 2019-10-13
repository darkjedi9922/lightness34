<?php namespace frame\views;

class Value extends View
{
    public static function getFolder(): string
    {
        return View::getFolder() . '/values';
    }
}