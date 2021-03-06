<?php namespace engine\users\actions;

use engine\users\User;
use frame\actions\ActionBody;
use frame\auth\InitAccess;
use frame\auth\Auth;
use frame\database\SqlDriver;

/**
 * Права: нужно быть залогиненым.
 */
class LogoutAction extends ActionBody 
{
    public function initialize(array $get)
    {
        InitAccess::accessLogged(true);
    }

    public function succeed(array $post, array $files)
    {
        $me = User::getMe();
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