<?php namespace engine\users;

use frame\database\Identity;

class Group extends Identity
{
    const GUEST_ID = 1;
    const ROOT_ID = 6;

    public static function getTable(): string
    {
        return 'user_groups';
    }
}