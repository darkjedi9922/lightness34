<?php namespace frame\views;

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
        ob_clean();
        parent::show();
    }
}