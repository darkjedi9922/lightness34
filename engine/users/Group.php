<?php namespace engine\users;

use frame\database\Identity;

class Group extends Identity
{
    const GUEST_ID = 1;
    const USER_ID = 2;
    const ROOT_ID = 6;

    public static function getTable(): string
    {
        return 'user_groups';
    }

    public function isSystem(): bool
    {
        return $this->id === static::GUEST_ID
            || $this->id === static::USER_ID
            || $this->id === static::ROOT_ID;
    }
}