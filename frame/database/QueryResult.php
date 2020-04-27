<?php namespace frame\database;

abstract class QueryResult
{
    /**
     * Возвращает Двумерный массив со всеми массивами-строками из результата.
     * Если в результате нет строк, вернет одномерный пустой массив.
     */
    public abstract function readAll(): array;

    /**
     * Считывает строку и возвращает ее в виде массива.
     * Если строк больше не осталось, вернет null.
     */
    public abstract function readLine(): ?array;

    /**
     * Считывает и возвращает первое значение в текущей строке. 
     * Значениями могут быть строки и null. Также вернет null, если
     * непрочитанных строк в результате больше не осталось.
     * @return null|string|int
     */
    public function readScalar()
    {
        $line = $this->readLine();
        if ($line) return current($line);
        else return null;
    }

    /**
     * Считывает колонку по ее индексу и возвращает в виде одномерного
     * индексного массива.
     */
    public abstract function readColumn(int $index): array;

    public abstract function count(): int;
    public abstract function seek(int $offset);
}