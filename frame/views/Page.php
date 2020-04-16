<?php namespace frame\views;

use function lightlib\ob_end_clean_all;

class Page extends Layouted
{
    public static function getNamespace(): string
    {
        return 'pages';
    }

    /**
     * {@inheritDoc}
     */
    public function show()
    {
        ob_end_clean_all();
        parent::show();
    }
}