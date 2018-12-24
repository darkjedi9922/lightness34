<?php namespace frame\views;

use frame\Core;

class Value extends View
{
    /**
     * Ищет сам view файл виджета
     * 
     * @see parent::find()
     */
    public static function find($name)
    {
        return parent::find(Core::$app->config->{'values.folder'} . '/' . $name);
    }
}