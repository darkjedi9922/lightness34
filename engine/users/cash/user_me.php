<?php namespace engine\users\cash;

use frame\cash\CashValue;
use frame\auth\Auth;
use engine\users\User;
use engine\users\Group;
use frame\tools\Client;
use frame\cash\CashStorage;
use frame\cash\StaticCashStorage;

class user_me extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return User
     */
    public static function get()
    {
        return self::cash('me', function () {
            $auth = new Auth;

            if ($auth->isLogged()) {
                $user = User::select(['sid' => $auth->getKey()]);
                if ($user) return $user;
            }

            return new User([
                'id' => Client::getId(),
                'login' => 'Гость',
                'group_id' => Group::GUEST_ID
            ]);
        });
    }
}