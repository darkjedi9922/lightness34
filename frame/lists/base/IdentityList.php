<?php namespace frame\lists\base;

use frame\database\SqlDriver;
use function lightlib\array_assemble;
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
        array $orderFields = ['id' => 'ASC']
    ) {
        $orderBy = !empty($orderFields) ?
            'ORDER BY ' . array_assemble($orderFields, ', ', ' ') : '';
        
        $this->query = SqlDriver::getDriver()->query(
            "SELECT * FROM {$identityClass::getTable()} $orderBy"
        );
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