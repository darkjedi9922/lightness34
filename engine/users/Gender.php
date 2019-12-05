<?php namespace engine\users;

use frame\database\Identity;

class Gender extends Identity
{
    const UNKNOWN_ID = 1;

    public static function getTable(): string
    {
        return 'user_genders';
    }

    public function isDefault(): bool
    {
        return $this->id === self::UNKNOWN_ID;
    }
}