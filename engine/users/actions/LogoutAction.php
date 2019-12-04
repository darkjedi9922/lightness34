<?php namespace engine\users\actions;

use frame\actions\Action;
use frame\tools\Init;
use frame\auth\Auth;
use frame\cash\database;

/**
 * Права: нужно быть залогиненым.
 */
class LogoutAction extends Action 
{
    protected function initialize(array $get)
    {
        Init::accessLogged(true);
    }

    protected function succeed(array $post, array $files)
    {
        $auth = new Auth;
        $auth->logout();
        database::get()->query(
            'UPDATE users SET online = 0 WHERE sid = "'.$auth->getKey().'"');
    }

    public function getSuccessRedirect(): ?string
    {
        return '/';
    }
}