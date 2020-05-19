<?php namespace engine\users;

use frame\database\Identity;
use frame\auth\GroupUser;
use frame\cash\StaticCashStorage;
use frame\auth\Auth;
use frame\tools\Client;
use engine\users\Group;
use frame\modules\Modules;
use frame\auth\UserRights;

class User extends Identity implements GroupUser
{
    const AVATAR_FOLDER = 'public/images/avatars';
    
    public static function getTable(): string
    {
        return 'users';
    }

    public static function getMe(): User
    {
        return StaticCashStorage::getDriver()->cash('user-me', function () {
            $auth = new Auth;

            if ($auth->isLogged()) {
                $user = self::select(['sid' => $auth->getKey()]);
                if ($user) return $user;
            }

            return new self([
                'id' => Client::getId(),
                'login' => 'Гость',
                'group_id' => Group::GUEST_ID
            ]);
        });
    }

    /**
     * @throws \Exception if there is not such module.
     * @throws \Exception if there is no such module rights.
     * @return UserRights
     */
    public static function getMyRights(string $module)
    {
        return StaticCashStorage::getDriver()->cash("my-$module-rights",
            function () use ($module) {
                $moduleInstance = Modules::getDriver()->findByName($module);
                if (!$moduleInstance)
                    throw new \Exception("There is no module $module.");

                $desc = $moduleInstance->createRightsDescription();
                if (!$desc) throw new \Exception(
                    "Module '$module' has no rights description."
                );

                $me = self::getMe();
                return new UserRights($desc, $moduleInstance->getId(), $me);
            }
        );
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