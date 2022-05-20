<?php namespace frame\lists\base;

use frame\database\Records;
use frame\lists\iterators\IdentityIterator;

class IdentityList implements BaseList
{
    private $query;
    private $iterator;

    /**
     * @param $orderFields Массив полей сортировки в виде [
     *  'field1' => 'ASC', 
     *  'field2' => 'DESC'
     * ].
     * Если сортировать не нужно - указать пустой массив.
     */
    public function __construct(
        string $identityClass, 
        array $orderFields = ['id' => 'ASC'],
        ?int $offset = null,
        ?int $limit = null
    ) {
        $query = Records::from($identityClass::getTable())->order($orderFields);
        if ($offset !== null && $limit !== null) {
            $query = $query->range($offset, $limit);
        }
        $this->query = $query->select();
        $this->iterator = new IdentityIterator($this->query, $identityClass);
    }

    public function getIterator(): \Iterator
    {
        return $this->iterator;
    }

    public function count()
    {
        return $this->query->count();
    }
}