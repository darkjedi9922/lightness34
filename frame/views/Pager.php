<?php namespace frame\views;

use frame\lists\Pager as ListPager;

class Pager extends Layouted
{
    private $pager;

    public static function getNamespace(): string
    {
        return 'pagers';
    }

    /** {@inheritDoc} */
    public static function getExtensions(): array
    {
        return ['php'];
    }

    /** {@inheritDoc} */
    public function __construct(ListPager $pager, string $name, string $layout = null)
    {
        parent::__construct($name, $layout);
        $this->pager = $pager;
    }

    public function getPager(): ListPager
    {
        return $this->pager;
    }
}