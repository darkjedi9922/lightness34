<?php namespace engine\users;

use frame\database\Identity;

class User extends Identity
{
    public static function getTable(): string
    {
        return 'users';
    }

    public function hasAvatar(): bool
    {
        return $this->avatar 
            && file_exists('public/images/avatars/' . $this->avatar);
    }

    public function getAvatarUrl(): string
    {
        if ($this->hasAvatar()) return 'public/images/avatars/' . $this->avatar;
        else return 'public/images/no-avatar.png';
    }
}