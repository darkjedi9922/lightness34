<?php namespace frame\views;

class Value extends View
{
    const FOLDER = 'views/values';

    /**
     * Ищет сам view файл виджета
     * 
     * @see parent::find()
     */
    public static function find(string $name): ?string
    {
        return parent::find(self::FOLDER . '/' . $name);
    }
}