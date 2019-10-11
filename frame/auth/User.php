<?php namespace frame\auth;

use frame\database\Identity;

class User extends Identity
{
    public static function getTable(): string
    {
        return 'users';
    }
}