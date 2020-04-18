<?php namespace engine\statistics\lists\history;

use frame\lists\base\IdentityList;
use frame\lists\base\BaseList;

abstract class HistoryList implements BaseList
{
    private $list = [];

    public function __construct()
    {
        $list = new IdentityList($this->getStatIdentityClass(), ['id' => 'DESC']);
        $this->list = $this->assembleArray($list);
    }

    public function count()
    {
        return count($this->list);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->list);
    }

    public function toArray(): array
    {
        return $this->list;
    }

    public abstract function getStatIdentityClass(): string;
    protected abstract function assembleArray(IdentityList $list): array;
}