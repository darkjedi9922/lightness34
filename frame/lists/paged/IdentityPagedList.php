<?php namespace frame\lists\paged;

use frame\cash\database;
use frame\database\Records;
use function lightlib\array_assemble;
use frame\lists\iterators\IdentityIterator;

abstract class IdentityPagedList extends PagedList
{
    private $result;
    private $iterator;

    public function __construct(int $page)
    {
        $whereFields = $this->getWhere();

        $table = $this->getIdentityClass()::getTable();
        $countAll = Records::select($table, $whereFields)->count('id');
        $pageLimit = $this->loadPageLimit();

        parent::__construct($page, $countAll, $pageLimit);

        $orderFields = $this->getOrderFields();
        $orderBy = !empty($orderFields) ? 
            'ORDER BY ' . array_assemble($orderFields, ', ', ' ') : '';
        
        $where = !(empty($whereFields)) ?
            'WHERE ' . array_assemble($whereFields, ' AND ', '=') : '';

        $from = $this->getPager()->getStartMaterialIndex();
        $this->result = database::get()->query(
            "SELECT * FROM $table $where $orderBy LIMIT $from, $pageLimit"
        );
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