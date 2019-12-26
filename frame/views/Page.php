<?php namespace frame\views;

class Page extends Layouted
{
    public static function getFolder(): string
    {
        return View::getFolder() . '/pages'; 
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