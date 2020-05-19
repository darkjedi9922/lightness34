<?php namespace frame\stdlib\drivers\database;

use frame\database\SqlDriver;
use frame\config\ConfigRouter;

class MySqlDriver extends SqlDriver
{
    private $mysqli;

    /**
     * @throws \Exception в случае неудачи подключения
     */
    public function __construct()
    {
        $config = ConfigRouter::getDriver()->findConfig('db');
        $host = $config->{'host'};
        $login = $config->{'username'};
        $password = $config->{'password'};
        $database = $config->{'dbname'};

        if (ini_get('mysqli.allow_persistent')) $host = "p:$host";
        $this->mysqli = new \mysqli($host, $login, $password, $database);
        $this->mysqli->query('SET NAMES UTF8'); // Фикс кодировки
        if ($this->mysqli->connect_errno)
            throw new \Exception($this->mysqli->connect_error);
        $this->mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
    }

    public function getLastInsertedId(): int
    {
        return $this->mysqli->insert_id;
    }

    protected function querySqlDirectly(string $sql)
    {
        $result = $this->mysqli->query($sql);
        if ($this->mysqli->errno) {
            throw new QueryException(
                $sql,
                $this->mysqli->error,
                $this->mysqli->errno
            );
        }
        return is_bool($result) ? $result : new MySqlResult($result);
    }
}