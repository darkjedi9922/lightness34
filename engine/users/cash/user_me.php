<?php namespace engine\users\cash;

use frame\tools\Cash;
use frame\auth\Auth;
use engine\users\User;
use engine\users\Group;
use frame\tools\Client;

class user_me extends Cash
{
    public static function get(): User
    {
        return self::cash('me', function () {
            $auth = new Auth;

            if ($auth->isLogged()) {
                $user = User::select(['sid' => $auth->getKey()]);
                if ($user) return $user;
            }

            $guest = new User;
            $guest->id = Client::getId();
            $guest->login = 'Гость';
            $guest->group_id = Group::GUEST_ID;
            return $guest;
        });
    }
}