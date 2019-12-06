<?php namespace frame\lists\paged;

use frame\cash\database;
use frame\database\Records;
use frame\lists\Pager;
use function lightlib\array_assemble;
use frame\lists\iterators\IdentityIterator;

abstract class IdentityPagedList implements \IteratorAggregate
{
    private $pager;
    private $iterator;

    public static abstract function getIdentityClass(): string;
    public static abstract function getPageLimit(): int;

    /**
     * Массив полей сортировки в виде ['field1' => 'ASC', 'field2' => 'DESC'].
     * Если сортировать не нужно, вернуть пустой массив.
     */
    public static function getOrderFields(): array { return []; }

    public function __construct(int $page)
    {
        $table = static::getIdentityClass()::getTable();
        $amount = Records::select($table)->count('id');
        $limit = static::getPageLimit();
        $this->pager = new Pager($page, $amount, $limit);

        $orderFields = static::getOrderFields();
        $orderBy = !empty($orderFields) ? 
            'ORDER BY ' . array_assemble($orderFields, ', ', ' ') : '';
        
        $this->iterator = new IdentityIterator(database::get()->query(
            "SELECT * FROM $table $orderBy 
            LIMIT {$this->pager->getStartMaterialIndex()}, $limit"
        ), $this->getIdentityClass());
    }

    public function getPager(): Pager
    {
        return $this->pager;
    }

    public function getIterator(): \Iterator
    {
        return $this->iterator;
    }
}