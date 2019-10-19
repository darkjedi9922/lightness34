<?php namespace cash;

use frame\tools\Cash;
use frame\database\Database as DB;

class database extends Cash 
{
    public static function get(): DB
    {
        return self::cash(function() {
            $config = config_core::get();
            $host = $config->{'database.host'};
            $username = $config->{'database.username'};
            $password = $config->{'database.password'};
            $dbname = $config->{'database.dbname'};
            return new DB($host, $username, $password, $dbname);
        });
    }
}