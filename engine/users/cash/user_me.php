<?php namespace engine\users\cash;

use frame\tools\Cash;
use frame\auth\Auth;
use engine\users\User;

class user_me extends Cash
{
    public static function get(): User
    {
        return self::cash(function () {
            $auth = new Auth;

            if ($auth->isLogged()) {
                $user = User::select(['sid' => $auth->getKey()]);
                if ($user) return $user;
            }

            $guest = new User;
            $guest->id = 0;
            $guest->login = 'Гость';
            $guest->group_id = 1;
            return $guest;
        });
    }
}