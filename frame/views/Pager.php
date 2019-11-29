<?php namespace frame\views;

use frame\Core;
use frame\lists\Pager as ListPager;

class Pager extends Layouted
{
    private $pager;

    /** {@inheritDoc} */
    public static function getFolder(): string
    {
        return View::getFolder() . '/pagers';
    }

    /** {@inheritDoc} */
    public static function getExtensions(): array
    {
        return ['php'];
    }

    /** {@inheritDoc} */
    public function __construct(ListPager $pager, string $name, string $layout = null)
    {
        if ($layout === null) $layout = Core::$app->config->{'pagers.defaultLayout'};
        parent::__construct($name, $layout);
        $this->pager = $pager;
    }

    public function getPager(): ListPager
    {
        return $this->pager;
    }
}