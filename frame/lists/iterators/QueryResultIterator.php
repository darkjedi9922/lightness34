<?php namespace frame\lists\iterators;

use frame\database\QueryResult;

abstract class QueryResultIterator implements \Iterator
{
    private $result;
    private $key = null;
    private $item = null;
    private $valid = false;

    public function __construct(QueryResult $result)
    {
        $this->result = $result;
    }

    public function next()
    {
        $line = $this->result->readLine();
        if (!$line) {
            $this->item = null;
            $this->key = null;
            $this->valid = false;
        } else {
            $this->item = $this->createItem($line);
            $this->key = $this->createKey($this->item, $line);
            $this->valid = true;
        }
    }

    public function current()
    {
        return $this->item;
    }

    public function key()
    {
        return $this->key;
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    public function rewind()
    {
        $this->result->seek(0);
        $this->next();
    }

    /** 
     * @return mixed
     */
    protected abstract function createItem(array $line);

    /**
     * @param mixed $current
     * @return mixed
     */
    protected abstract function createKey($current, array $line);
}