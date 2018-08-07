<?php namespace frame\database;

use frame\database\QueryResult;

/**
 * Данный класс использует MySQLi.
 */
class Database 
{
    /**
     * @var \mysqli 
     */
    private $mysqli;

    /**
     * @param string $host
     * @param string $login
     * @param string $password
     * @param string $database
     * @throws \Exception в случае неудачи подключения
     */
	public function __construct(string $host, string $login, string $password, string $database) 
	{
        $this->mysqli = new \mysqli($host, $login, $password, $database);
        $this->mysqli->query('SET NAMES UTF8'); // Фикс кодировки
        if ($this->mysqli->connect_errno) throw new \Exception($this->mysqli->connect_error);
	}	

    /**
     * Выполняет SQL-запрос
     * @param string $sql SQL-запрос
     * @return QueryResult|bool
     */
    public function query($sql)
    {
        $result = $this->mysqli->query($sql);
        if ($this->mysqli->errno) throw new \Exception($this->mysqli->error);
        return is_bool($result) ? $result : new QueryResult($result);
    }

    /**
     * @return int Значение поля AUTO_INCREMENT, которое было затронуто предыдущим запросом. 
     * Возвращает ноль, если предыдущий запрос не затронул таблицы, содержащие поле AUTO_INCREMENT
     */
	public function getLastInsertedId()
	{
		return $this->mysqli->insert_id;
	}
}