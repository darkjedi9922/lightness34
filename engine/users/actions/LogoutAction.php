<?php namespace engine\users\actions;

use frame\actions\ActionBody;
use frame\tools\Init;
use frame\auth\Auth;
use frame\cash\database;

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