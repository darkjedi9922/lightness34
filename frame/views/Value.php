<?php namespace frame\views;

use frame\Core;

class Value extends View
{
    const FOLDER = 'view/values';

    /**
     * Ищет сам view файл виджета
     * 
     * @see parent::find()
     */
    public static function find($name)
    {
        return parent::find(self::FOLDER . '/' . $name);
    }
}