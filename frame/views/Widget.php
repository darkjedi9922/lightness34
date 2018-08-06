<?php namespace frame\views;

use frame\Core;

class Widget extends View
{
    /**
     * Ищет сам view файл виджета
     * 
     * @see parent::find()
     */
    public static function find($name)
    {
        return parent::find(Core::$config->{'widgets.folder'} . '/' . $name);
    }

    /**
     * {@inheritDoc}
     */
    public function __construct($name, $layout = null)
    {
        parent::__construct($name, $layout);
        if ($this->layoutname === null) $this->layoutname = Core::$config->{'widgets.defaultLayout'};
    }
}