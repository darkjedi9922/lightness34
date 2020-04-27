<?php namespace frame\stdlib\drivers\database;

use frame\database\QueryResult;

class MySqlResult extends QueryResult
{
    private $result;

    public function __construct(\mysqli_result $result)
    {
        $this->result = $result;
    }

    public function readAll(): array
    {
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    public function readLine(): ?array
    {
        return $this->result->fetch_array(MYSQLI_ASSOC);
    }

    public function readColumn(int $index): array
    {
        $all = $this->result->fetch_all(MYSQLI_NUM);
        return array_column($all, $index);
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