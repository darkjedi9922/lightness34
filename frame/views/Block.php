<?php namespace frame\views;

use frame\Core;

class Block extends Layouted
{
    public static function getFolder(): string
    {
        return View::getFolder() . '/blocks';
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