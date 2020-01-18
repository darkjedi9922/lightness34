<?php namespace engine\users;

use frame\database\Identity;
use frame\modules\GroupUser;

class User extends Identity implements GroupUser
{
    const AVATAR_FOLDER = 'public/images/avatars';
    
    public static function getTable(): string
    {
        return 'users';
    }

    public function getGroupId(): int
    {
        // TODO: group_id изначально может быть не установлен, нужна проверка на его
        // существование. Если его нет возвращать id группы гостя.
        return $this->group_id;
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