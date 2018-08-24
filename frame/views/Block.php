<?php namespace frame\views;

use frame\Core;

class Block extends View
{
    /**
     * Ищет сам view файл блока
     * 
     * @see parent::find()
     */
    public static function find($name)
    {
        return parent::find(Core::$app->config->{'blocks.folder'} . '/' . $name);
    }

    /**
     * {@inheritDoc}
     */
    public function __construct($name, $layout = null)
    {
        parent::__construct($name, $layout);
        if ($this->layoutname === null) $this->layoutname = Core::$app->config->{'blocks.defaultLayout'};
    }
}