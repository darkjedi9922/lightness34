<?php namespace tests\cash\examples;

use frame\cash\CashValue;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;

class cash_example extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    public static function get(int $number)
    {
        return self::cash('test', function() use ($number) {
            return $number;
        });
    }
}