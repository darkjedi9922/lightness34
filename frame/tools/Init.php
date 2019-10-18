<?php namespace frame\tools;

use frame\errors\HttpError;
use frame\auth\Auth;

class Init
{
    /** @throws HttpError NOT_FOUND */
    public static function require(bool $expr)
    {
        if (!$expr) throw new HttpError(HttpError::NOT_FOUND);
    }

    /** @throws HttpError NOT_FOUND */
    public static function requireGet(string $name): string
    {
        $router = CoreObjects::getRouter();
        if (!$router->isSetArgument($name)) throw new HttpError(HttpError::NOT_FOUND, 
            'The '.$name.' url argument does not exist.');
        return $router->getArgument($name);
    }

    /** @throws HttpError FORBIDDEN */
    public static function access(bool $expr)
    {
        if ($expr === false) throw new HttpError(HttpError::FORBIDDEN);
    }

    /** @throws HttpError FORBIDDEN */
    public static function accessLogged(bool $beLogged)
    {
        $auth = new Auth;
        static::access($auth->isLogged() === $beLogged);
    }
}