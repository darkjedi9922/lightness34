<?php namespace engine\users\actions;

use frame\actions\Action;
use frame\tools\Init;
use frame\auth\Auth;
use cash\database;

/**
 * Права: нужно быть залогиненым.
 */
class LogoutAction extends Action 
{
    protected function initialization()
    {
        Init::accessLogged(false);
    }

    protected function succeed()
    {
        $auth = new Auth;
        $auth->logout();
        database::get()->query(
            'UPDATE users SET online = 0 WHERE sid = "'.$auth->getKey().'"');
    }

    protected function getSuccessRedirect(): string
    {
        return '/';
    }
}