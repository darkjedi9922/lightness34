<?php namespace engine\users;

use frame\database\Identity;
use frame\auth\UserGroup;
use frame\cash\StaticCashStorage;
use engine\users\User;

class Group extends Identity implements UserGroup
{
    const GUEST_ID = 1;
    const USER_ID = 2;

    public static function getTable(): string
    {
        return 'user_groups';
    }

    public static function getMine(): Group
    {
        return StaticCashStorage::getDriver()->cash('my-group', function () {
            return self::selectIdentity(User::getMe()->getGroupId());
        });
    }

    public function isSystem(): bool
    {
        return $this->id === static::GUEST_ID
            || $this->id === static::USER_ID
            || $this->id === static::ROOT_ID;
    }
}