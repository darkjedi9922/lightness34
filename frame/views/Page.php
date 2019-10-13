<?php namespace frame\views;

use frame\Core;

class Page extends Layouted
{
    public static function getFolder(): string
    {
        return View::getFolder() . '/pages'; 
    }

    /**
     * {@inheritDoc}
     */
    public function __construct($name, $layout = null)
    {
        parent::__construct($name, $layout);
        if ($this->layoutname === null) $this->layoutname = Core::$app->config->{'pages.defaultLayout'};
    }

    /**
     * {@inheritDoc}
     */
    public function show()
    {
        ob_clean();
        parent::show();
    }
}