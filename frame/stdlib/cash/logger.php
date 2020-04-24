<?php namespace frame\stdlib\cash;

use frame\cash\CashValue;
use frame\tools\Logger as FrameLogger;
use frame\stdlib\cash\config;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;

class logger extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return FrameLogger
     */
    public static function get()
    {
        return self::cash('app-logger', function() {
            $file = config::get('core')->{'log.file'};
            return new FrameLogger(ROOT_DIR . '/' . $file);
        });
    }
}