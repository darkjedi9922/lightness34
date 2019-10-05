<?php namespace cash;

use frame\tools\Cash;
use frame\database\Database as DB;
use frame\config\DefaultedConfig;
use frame\config\Json;

class database extends Cash 
{
    public static function get(): DB
    {
        return self::cash(function() {
            $config = new DefaultedConfig(
                new Json('config/core.json'),
                new Json('config/default/core.json')
            );
            $host = $config->{'database.host'};
            $username = $config->{'database.username'};
            $password = $config->{'database.password'};
            $dbname = $config->{'database.dbname'};
            return new DB($host, $username, $password, $dbname);
        });
    }
}