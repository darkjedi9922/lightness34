<?php namespace frame\database;

use frame\core\Driver;
use frame\events\Events;
use frame\database\QueryResult;

abstract class SqlDriver extends Driver
{
    const EVENT_QUERY_START = 'db-query-start';
    const EVENT_QUERY_END = 'db-query-end';

    /**
     * Выполняет SQL-запрос.
     * @return QueryResult|bool
     */
    public function query(string $sql)
    {
        Events::getDriver()->emit(self::EVENT_QUERY_START, $sql);
        $result = $this->querySqlDirectly($sql);
        Events::getDriver()->emit(self::EVENT_QUERY_END, $sql);
        return $result;
    }

    /**
     * @return int Значение поля AUTO_INCREMENT, которое было затронуто предыдущим 
     * запросом. Возвращает ноль, если предыдущий запрос не затронул таблицы, 
     * содержащие поле AUTO_INCREMENT.
     */
	public abstract function getLastInsertedId(): int;

    /**
     * @return QueryResult|bool
     */
    protected abstract function querySqlDirectly(string $sql); 
}