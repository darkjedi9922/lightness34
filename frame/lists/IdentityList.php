<?php namespace frame\lists;

use frame\database\Identity;
use cash\database;

class IdentityList implements IterableList
{
    private $list;
    private $identityClass;
    /** @var Identity|null $item */
    private $item = null;

    public function __construct(string $identityClass)
    {
        $this->identityClass = $identityClass;
        $this->list = database::get()->query(
            'SELECT * FROM ' . $identityClass::getTable() . ' ORDER BY id'
        );
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

    /** @return Identity */
    public function current()
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