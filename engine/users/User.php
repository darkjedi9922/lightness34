<?php namespace engine\users;

use frame\database\Identity;

class User extends Identity
{
    public static function getTable(): string
    {
        return 'users';
    }
}