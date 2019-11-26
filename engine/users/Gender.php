<?php namespace engine\users;

use frame\database\Identity;

class Gender extends Identity
{
    public static function getTable(): string
    {
        return 'user_genders';
    }
}