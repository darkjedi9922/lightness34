<?php namespace frame\stdlib\cash;

use frame\cash\CashValue;
use frame\config\ConfigRouter;
use frame\config\NamedConfig;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;

class config extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return NamedConfig
     */
    public static function get(string $name)
    {
        return self::cash($name, function() use ($name) {
            return ConfigRouter::getDriver()->findConfig($name);
        });
    }
}