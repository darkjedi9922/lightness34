<?php namespace frame\views;

class Block extends Layouted
{
    public static function getFolder(): string
    {
        return View::getFolder() . '/blocks';
    }
}