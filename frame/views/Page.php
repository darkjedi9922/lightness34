<?php namespace frame\views;

use function lightlib\ob_restart_all;

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
        ob_restart_all();
        parent::show();
    }
}