<?php namespace engine\users\cash;

use frame\cash\CashValue;
use engine\users\Group;
use frame\cash\CashStorage;
use frame\cash\StaticCashStorage;

class my_group extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return Group
     */
    public static function get()
    {
        return self::cash('mg', function() {
            return Group::selectIdentity(user_me::get()->group_id);
        });
    }
}