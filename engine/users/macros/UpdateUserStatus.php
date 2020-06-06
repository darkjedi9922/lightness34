<?php namespace engine\users\macros;

use engine\users\User;
use frame\auth\Auth;
use frame\events\Macro;
use frame\http\Client;

class UpdateUserStatus extends Macro
{
    public function exec(...$args)
    {
        if (!(new Auth)->isLogged()) return;
        $me = User::getMe();
        $me->online = true;
        $me->last_online_time = time();
        $me->last_user_agent = Client::getUserAgent();
        $me->update();
    }
}