<?php namespace frame\views;

use frame\Core;

class Widget extends Layouted
{
    const FOLDER = 'views/widgets';

    /**
     * Ищет сам view файл виджета
     * 
     * @see parent::find()
     */
    public static function find($name)
    {
        return parent::find(self::FOLDER . '/' . $name);
    }

    /**
     * {@inheritDoc}
     */
    public function __construct($name, $layout = null)
    {
        parent::__construct($name, $layout);
        if ($this->layoutname === null) $this->layoutname = Core::$app->config->{'widgets.defaultLayout'};
    }
}