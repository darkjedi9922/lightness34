<?php namespace frame\lists\paged;

use frame\lists\Pager;

abstract class PagedList implements \IteratorAggregate
{
    private $pager;

    public function __construct(int $page, int $countAll, int $pageLimit)
    {
        $this->pager = new Pager($page, $countAll, $pageLimit);
    }

    public abstract function countOnPage(): int;

    public abstract function getIterator(): \Iterator;

    public function countAll(): int
    {
        return $this->pager->countAllMaterials();
    }

    public function getPageLimit()
    {
        return $this->pager->countPageLimit();
    }

    public function getPager(): Pager
    {
        return $this->pager;
    }
}