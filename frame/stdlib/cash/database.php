<?php namespace frame\stdlib\cash;

use frame\cash\CashValue;
use frame\database\Database as DB;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;

class database extends CashValue 
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return DB
     */
    public static function get()
    {
        return self::cash('db', function() {
            $config = config::get('db');
            $host = $config->{'host'};
            $username = $config->{'username'};
            $password = $config->{'password'};
            $dbname = $config->{'dbname'};
            return new DB($host, $username, $password, $dbname);
        });
    }
}