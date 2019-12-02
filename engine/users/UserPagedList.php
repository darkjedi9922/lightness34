<?php namespace engine\users;

use frame\lists\IdentityPagedList;
use engine\users\User;
use frame\cash\config;

class UserPagedList extends IdentityPagedList
{
    public static function getIdentityClass(): string
    {
        return User::class;
    }

    public static function getPageLimit(): int
    {
        return config::get('users')->{'list.amount'};
    }

    public static function getOrderFields(): array
    {
        return ['id' => 'ASC'];
    }
}