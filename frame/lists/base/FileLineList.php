<?php namespace frame\lists\base;

/**
 * Если файл слишком большой, может выдать ошибку о переполнении памяти.
 * The key is a line number.
 * The value is the line.
 */
class FileLineList implements BaseList
{
    private $file;
    private $lines = [];

    public function __construct(string $file)
    {
        $this->file = $file;
        if (file_exists($this->file)) $this->lines = file($this->file);
    }

    public function rewind()
    {
        reset($this->lines);
    }

    public function current()
    {
        return current($this->lines);
    }

    public function key()
    {
        return key($this->lines) + 1;
    }

    public function next()
    {
        next($this->lines);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

    public function count()
    {
        return count($this->lines);
    }
}