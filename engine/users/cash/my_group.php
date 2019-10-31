<?php namespace engine\users\cash;

use frame\tools\Cash;
use engine\users\Group;

class my_group extends Cash
{
    public static function get(): Group
    {
        return self::cash('mg', function() {
            return Group::selectIdentity(user_me::get()->group_id);
        });
    }
}