<?php namespace frame\database;

use frame\database\Database;
use frame\Core;

use function lightlib\array_assemble;

/**
 * Работает с набором записей из таблицы БД
 */
class Records
{
    /**
     * @var string Имя таблицы
     */
    public $table;

    /**
     * @var array Ассоциативный массив, где ключи - имена полей, а значения - значения этих полей
     */
    public $where;

    /**
     * @var Database
     */
    public $db;

    /**
     * Создает экземпляр класса
     * 
     * @param string $table Имя таблицы в БД
     * @param array $where Ассоциативный массив, где ключи - имена полей, а значения - значения этих полей
     * @return Records
     */
    public static function select($table, $where = [])
    {
        $records = new static;
        $records->table = $table;
        $records->where = $where;
        $records->db = Core::$app->db;
        return $records;
    }

    /**
     * Метод select() предоставляет более удобный, красивый и семантический 
     * способ создания экземпляра.
     */
    private function __construct() {}

    /**
     * Возвращает количество записей заданного поля
     * @param string $field Имя поля
     * @return int
     */
    public function count($field)
    {
        return (int) $this->db->query('SELECT COUNT(' . $field . ')' . ' FROM ' . $this->table . $this->getWhere())->readScalar();
    }

    /**
     * Загружает заданные поля из записей. Если поля не указаны, будут загружены все.
     * @param array $fields Поля
     * @return QueryResult
     */
    public function load($fields = [])
    {
        $fields = empty($fields) ? '*' : implode(', ', $fields);
        return $this->db->query('SELECT '.$fields.' FROM ' . $this->table . $this->getWhere());
    }

    /**
     * Изменяет заданные данные в записях
     * @param array $data Ассоциативный массив, где ключи - имена полей, которые нужно изменить,
     * а значения - новые значения этих полей
     */
    public function update($data)
    {
        if (!empty($data)) {
            $set = array_assemble($this->addAssocQuotes($data), ', ', ' = ');
            $this->db->query('UPDATE ' . $this->table . ' SET ' . $set . $this->getWhere());
        }
    }

    /**
     * Вставляет в таблицу $table запись, с данными, которые были заданы в массиве $where
     * @see $table
     * @see $where
     */
    public function insert()
    {
        $keys = implode(array_keys($this->where), ', ');
        $values = implode($this->addIndexQuotes(array_values($this->where)), ', ');
        $this->db->query('INSERT INTO ' . $this->table . ' (' . $keys . ') VALUES (' . $values . ')');
        return $this->db->getLastInsertedId();
    }

    /**
     * Удаляет из таблицы записи
     */
    public function delete()
    {
        $this->db->query('DELETE FROM ' . $this->table . $this->getWhere());
    }

    /**
     * Формирует часть WHERE в SQL запросе
     * @return string
     */
    private function getWhere()
    {
        if (empty($this->where)) return '';
        $where = $this->addAssocQuotes($this->where);
        return ' WHERE ' . array_assemble($where, ' AND ', ' = ');
    }

    /**
     * Окружает каждое значение в индексном массиве кавычками. 
     * Они нужны для SQL запроса.
     * @param array $array Индексный массив
     * @return array
     */
    private function addIndexQuotes($array)
    {
        for ($i = 0, $c = count($array); $i < $c; ++$i) $array[$i] = '"' . $array[$i] . '"';
        return $array;
    }

    /**
     * Окружает каждое значение в ассоциативном массиве кавычками. 
     * Они нужны для SQL запроса.
     * @param array $array Ассоциативный массив
     * @return array
     */
    private function addAssocQuotes($array)
    {
        foreach ($array as $key => $value) $array[$key] = '"' . $value . '"';
        return $array;
    }
}