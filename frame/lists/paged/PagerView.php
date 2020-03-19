<?php namespace frame\lists\paged;

class PagerView extends \frame\views\Layouted
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
    public function __construct(PagerModel $pager, string $name, string $layout = null)
    {
        parent::__construct($name, $layout);
        $this->pager = $pager;
    }

    public function getPager(): PagerModel
    {
        return $this->pager;
    }
}