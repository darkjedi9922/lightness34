<?php namespace engine\users\macros;

use engine\users\cash\user_me;
use frame\auth\Auth;
use frame\events\Macro;
use frame\tools\Client;

class UpdateUserStatus extends Macro
{
    public function exec(...$args)
    {
        if (!(new Auth)->isLogged()) return;
        $me = user_me::get();
        $me->online = true;
        $me->last_online_time = time();
        $me->last_user_agent = Client::getUserAgent();
        $me->update();
    }
}