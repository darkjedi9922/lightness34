<?php namespace frame\cash;

use frame\tools\Cash;
use frame\database\Database as DB;

class database extends Cash 
{
    public static function get(): DB
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