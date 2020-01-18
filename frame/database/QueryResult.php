<?php namespace frame\database;

class QueryResult
{
    private $result;

    public function __construct(\mysqli_result $result)
    {
        $this->result = $result;
    }

    /**
     * Возвращает Двумерный массив со всеми массивами-строками из результата.
     * Если в результате нет строк, вернет одномерный пустой массив.
     */
    public function readAll(): array
    {
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Считывает строку и возвращает ее в виде массива.
     * Если строк больше не осталось, вернет null.
     */
    public function readLine(): ?array
    {
        return $this->result->fetch_array(MYSQLI_ASSOC);
    }

    /**
     * Считывает колонку по ее индексу и возвращает в виде одномерного
     * индексного массива.
     */
    public function readColumn(int $index): array
    {
        $all = $this->result->fetch_all(MYSQLI_NUM);
		return array_column($all, $index);
    }

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

    public function count(): int
    {
        return $this->result->num_rows;
    }

    public function seek(int $offset)
    {
        $this->result->data_seek($offset);
    }
}