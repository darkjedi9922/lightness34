<?php namespace frame\stdlib\cash;

use frame\cash\CashValue;
use frame\config\ConfigRouter;
use frame\config\NamedConfig;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;
use Exception;

class config extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return NamedConfig
     * @throws Exception if ConfigRouter can't find config.
     */
    public static function get(string $name)
    {
        return self::cash($name, function() use ($name) {
            $config = ConfigRouter::getDriver()->findConfig($name);
            if ($config) return $config;
            throw new Exception(
                "Driver " . ConfigRouter::class .
                " could not find config '$name'"
            );
        });
    }
}