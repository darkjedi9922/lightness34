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

    public function getIterator(): \Generator
    {
        foreach ($this->lines as $key => $line) 
            yield $key + 1 => $line; 
    }

    public function count()
    {
        return count($this->lines);
    }
}