<?php namespace frame\lists;

use cash\database;
use frame\database\Records;
use frame\database\Identity;
use frame\lists\Pager;
use function lightlib\array_assemble;

abstract class IdentityPagedList implements IterableList
{
    private $list;
    private $pager;

    /** @var frame\database\Identity|null $item */
    private $item = null;

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
        
        $this->list = database::get()->query(
            "SELECT * FROM $table $orderBy 
            LIMIT {$this->pager->getStartMaterialIndex()}, $limit");
    }

    public function getPager(): Pager
    {
        return $this->pager;
    }

    public function count(): int
    {
        return $this->list->count();
    }

    public function coundAll(): int
    {
        return $this->pager->countAllMaterials();
    }

    public function next()
    {
        $info = $this->list->readLine();
        if (!$info) $this->item = null;
        else {
            $class = static::getIdentityClass();
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