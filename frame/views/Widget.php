<?php namespace frame\views;

class Widget extends Layouted
{
    public static function getFolder(): string
    {
        return View::getFolder() . '/widgets';
    }
}