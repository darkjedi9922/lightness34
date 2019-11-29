<?php namespace engine\users;

use frame\database\Identity;

class User extends Identity
{
    const AVATAR_FOLDER = 'public/images/avatars';
    
    public static function getTable(): string
    {
        return 'users';
    }

    public function hasAvatar(): bool
    {
        return $this->avatar 
            && file_exists(self::AVATAR_FOLDER . '/' . $this->avatar);
    }

    public function getAvatarUrl(): string
    {
        if ($this->hasAvatar()) return self::AVATAR_FOLDER . '/' . $this->avatar;
        else return 'public/images/no-avatar.png';
    }
}