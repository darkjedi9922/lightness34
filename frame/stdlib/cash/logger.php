<?php namespace frame\stdlib\cash;

use frame\cash\CashValue;
use frame\stdlib\cash\config;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;
use frame\tools\logging\PagedLogger;

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
            $config = config::get('core');
            $file = $config->{'log.file'};
            $byteLimit = $config->{'log.pageByteLimit'};
            return new PagedLogger(ROOT_DIR . '/' . $file, $byteLimit);
        });
    }
}