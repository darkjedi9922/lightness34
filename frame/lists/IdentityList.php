<?php namespace frame\lists;

use frame\database\Identity;
use frame\cash\database;

use function lightlib\array_assemble;

class IdentityList implements IterableList
{
    private $list;
    private $identityClass;
    /** @var Identity|null $item */
    private $item = null;

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
        $this->identityClass = $identityClass;
        $orderBy = !empty($orderFields) ?
            'ORDER BY ' . array_assemble($orderFields, ', ', ' ') : '';
        $this->list = database::get()->query(
            "SELECT * FROM {$identityClass::getTable()} $orderBy");
    }

    public function count(): int
    {
        return $this->list->count();
    }

    public function next()
    {
        $info = $this->list->readLine();
        if (!$info) $this->item = null;
        else {
            $class = $this->identityClass;
            $this->item = new $class($info);
        }
    }

    public function current(): ?Identity
    {
        return $this->item;
    }

    /**
     * @return int Identity id or -1.
     */
    public function key(): int
    {
        if ($this->item) return $this->item->id;
        return -1;
    }

    public function valid()
    {
        return $this->item;
    }

    public function rewind()
    {
        $this->list->seek(0);
        $this->next();
    }
}