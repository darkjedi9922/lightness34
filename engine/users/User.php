<?php namespace engine\users;

use frame\database\Identity;

class User extends Identity
{
    public static function getTable(): string
    {
        return 'users';
    }

    public function getAvatarUrl(): string
    {
        if ($this->avatar) return 'public/images/avatars/' . $this->avatar;
        else return 'public/images/no-avatar.png';
    }
}