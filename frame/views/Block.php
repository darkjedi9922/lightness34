<?php namespace frame\views;

use frame\Core;

class Block extends Layouted
{
    const FOLDER = 'views/blocks';

    /**
     * Ищет сам view файл блока
     * 
     * @see parent::find()
     */
    public static function find(string $name): ?string
    {
        return parent::find(self::FOLDER . '/' . $name);
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