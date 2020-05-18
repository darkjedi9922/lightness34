<?php namespace frame\stdlib\cash;

use frame\cash\CashValue;
use frame\stdlib\cash\config;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;
use frame\tools\logging\SimpleLogger;

class logger extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return \frame\tools\logging\Logger
     */
    public static function get()
    {
        return self::cash('app-logger', function() {
            $dir = config::get('core')->{'log.dir'};
            $date = date('d-m-Y');
            return new SimpleLogger("$dir/$date.txt");
        });
    }
}