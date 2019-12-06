<?php namespace frame\lists\base;

use frame\cash\database;
use function lightlib\array_assemble;
use frame\lists\iterators\IdentityIterator;

class IdentityList implements \IteratorAggregate
{
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
        
        $this->iterator = new IdentityIterator(database::get()->query(
            "SELECT * FROM {$identityClass::getTable()} $orderBy"
        ), $identityClass);
    }

    public function getIterator(): \Iterator
    {
        return $this->iterator;
    }
}