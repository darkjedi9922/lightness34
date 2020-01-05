<?php namespace frame\database;

use frame\database\Database;

use function lightlib\array_assemble;

/**
 * Работает с набором записей из таблицы БД
 */
class Records
{
    /** @var string Имя таблицы */
    private $table;

    /**
     * @var array Ассоциативный массив, где ключи - имена полей, 
     * а значения - значения этих полей
     */
    private $where = [];

    private $orderBy = [];

    private $limit = [null, null];

    /** @var Database */
    private $db;

    /**
     * Создает экземпляр класса
     * 
     * @param array $where Ассоциативный массив, где ключи - имена полей, а значения 
     * - значения этих полей
     */
    public static function from(string $table, array $where = []): Records
    {
        $records = new static;
        $records->table = $table;
        $records->where = $where;
        $records->db = \frame\cash\database::get();
        return $records;
    }

    /**
     * Метод from() предоставляет более удобный, красивый и семантический 
     * способ создания экземпляра.
     */
    private function __construct() {}

    public function getWhereFields(): array
    {
        return $this->where;
    }

    public function getOrderFields(): array
    {
        return $this->orderBy;
    }

    public function getRangeStart(): ?int
    {
        return $this->limit[0];
    }

    public function getRangeLimit(): ?int
    {
        return $this->limit[1];
    }

    /**
     * @param array $fields ['field' => 'ASC'|'DESC']
     */
    public function order(array $fields): self
    {
        $this->orderBy = $fields;
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = [null, $limit];
        return $this;
    }

    public function range(int $from, int $limit): self
    {
        $this->limit = [$from, $limit];
        return $this;
    }

    /**
     * Возвращает количество записей заданного поля
     */
    public function count(string $field): int
    {
        $from = $this->table;
        $where = $this->assembleWhere();
        return (int) $this->db->query("SELECT COUNT(`$field`) FROM `$from` $where")
            ->readScalar();
    }

    /**
     * Загружает заданные поля из записей. Если поля не указаны, будут загружены все.
     */
    public function select(array $fields = []): QueryResult
    {
        $fields = empty($fields) ? 
            '*' : 
            implode(', ', $this->addIndexQuotes($fields, '`'));
        $from = $this->table;
        $where = $this->assembleWhere();
        $orderBy = $this->assembleOrderBy();
        $limit = $this->assembleLimit();
        return $this->db->query("SELECT $fields FROM $from $where $orderBy $limit");
    }

    /**
     * Изменяет заданные данные в записях
     * @param array $data Ассоциативный массив, где ключи - имена полей, которые 
     * нужно изменить, а значения - новые значения этих полей
     */
    public function update(array $data)
    {
        if (!empty($data)) {
            $what = $this->table;
            $set = array_assemble($this->addAssocQuotes($data), ', ', ' = ');
            $where = $this->assembleWhere();
            $this->db->query("UPDATE `$what` SET $set $where");
        }
    }

    /**
     * Вставляет в таблицу $table запись, с данными, которые были заданы в 
     * массиве $where при создании и значениями $values.
     * @param array $values Ассоциативный массив, аналогичный $where.
     * @see $table
     * @see $where
     */
    public function insert(array $values = []): int
    {
        $into = $this->table;
        $set = $this->addAssocQuotes(array_merge($this->where, $values));
        $keys = implode(', ', array_keys($set));
        $values = implode(', ', array_values($set));
        $this->db->query("INSERT INTO `$into` ($keys) VALUES ($values)");
        return $this->db->getLastInsertedId();
    }

    /**
     * Удаляет из таблицы записи
     */
    public function delete()
    {
        $from = $this->table;
        $where = $this->assembleWhere();
        $this->db->query("DELETE FROM `$from` $where");
    }

    /**
     * Формирует часть WHERE в SQL запросе
     */
    private function assembleWhere(): string
    {
        if (empty($this->where)) return '';
        $where = $this->addAssocQuotes($this->where);
        return 'WHERE ' . array_assemble($where, ' AND ', ' = ');
    }

    private function assembleOrderBy(): string
    {
        if (empty($this->orderBy)) return '';
        $orderBy = [];
        foreach ($this->orderBy as $field => $order) $orderBy["`$field`"] = $order;
        return 'ORDER BY ' . array_assemble($orderBy, ', ', ' ');
    }

    /**
     * Формирует часть LIMIT в SQL запросе
     */
    private function assembleLimit(): string
    {
        list($from, $limit) = $this->limit;
        if ($limit === null) return '';
        if ($from === null) return "LIMIT $limit";
        else return "LIMIT $from, $limit"; 
    }

    /**
     * Окружает каждое значение в индексном массиве кавычками. 
     * Они нужны для SQL запроса.
     * @param array $array Индексный массив
     */
    private function addIndexQuotes(array $array, string $quoteSymbol): array
    {
        for ($i = 0, $c = count($array); $i < $c; ++$i) 
            $array[$i] = "$quoteSymbol" . $array[$i] . "$quoteSymbol";
        return $array;
    }

    /**
     * Окружает каждое значение в ассоциативном массиве кавычками. 
     * Они нужны для SQL запроса.
     * @param array $array Ассоциативный массив
     */
    private function addAssocQuotes(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) { 
            $value = str_replace("'", "\\'", $value);
            $result["`$key`"] = "'$value'";
        }
        return $result;
    }
}