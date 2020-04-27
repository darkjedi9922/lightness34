<?php namespace engine\users\actions;

use engine\users\cash\user_me;
use frame\actions\ActionBody;
use frame\tools\Init;
use frame\auth\Auth;
use frame\database\SqlDriver;

/**
 * Права: нужно быть залогиненым.
 */
class LogoutAction extends ActionBody 
{
    public function initialize(array $get)
    {
        Init::accessLogged(true);
    }

    public function succeed(array $post, array $files)
    {
        $me = user_me::get();
        $me->online = 0;
        $me->update();
        
        $auth = new Auth;
        $auth->logout();
        SqlDriver::getDriver()->query(
            'UPDATE users SET online = 0 WHERE sid = "'.$auth->getKey().'"');
    }

    public function getSuccessRedirect(): ?string
    {
        return '/';
    }
}