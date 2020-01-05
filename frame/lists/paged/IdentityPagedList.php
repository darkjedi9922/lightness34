<?php namespace frame\lists\paged;

use frame\database\Records;
use frame\lists\iterators\IdentityIterator;

abstract class IdentityPagedList extends PagedList
{
    private $result;
    private $iterator;

    public function __construct(int $page)
    {
        $where = $this->getWhere();

        $table = $this->getIdentityClass()::getTable();
        $countAll = Records::from($table, $where)->count('id');
        $pageLimit = $this->loadPageLimit();

        parent::__construct($page, $countAll, $pageLimit);

        $this->result = Records::from($table, $where)
            ->order($this->getOrderFields())
            ->range($this->getPager()->getStartMaterialIndex(), $pageLimit)
            ->select();

        $this->iterator = new IdentityIterator(
            $this->result,
            $this->getIdentityClass()
        );
    }

    public abstract function getIdentityClass(): string;

    /**
     * Массив полей сортировки в виде ['field1' => 'ASC', 'field2' => 'DESC'].
     * Если сортировать не нужно, вернуть пустой массив.
     */
    public function getOrderFields(): array { return []; }

    public function getWhere(): array { return []; }

    public function countOnPage(): int
    {
        return $this->result->count();
    }

    public function getIterator(): \Iterator
    {
        return $this->iterator;
    }

    protected abstract function loadPageLimit(): int;
}