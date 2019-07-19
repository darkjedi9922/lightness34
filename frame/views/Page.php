<?php namespace frame\views;

use frame\Core;

class Page extends Layouted
{
    const FOLDER = 'views/pages';

    /**
     * Ищет сам view файл страницы
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