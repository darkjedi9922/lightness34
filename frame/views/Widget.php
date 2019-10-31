<?php namespace frame\views;

use frame\Core;

class Widget extends Layouted
{
    public static function getFolder(): string
    {
        return View::getFolder() . '/widgets';
    }

    /**
     * {@inheritDoc}
     */
    public function __construct($name, $layout = null)
    {
        parent::__construct($name, $layout);
        if ($this->getLayout() === null) 
            $this->setLayout(Core::$app->config->{'widgets.defaultLayout'});
    }
}