<?php namespace frame\tools;

use engine\users\cash\my_rights;
use frame\route\HttpError;
use frame\auth\Auth;
use engine\users\cash\user_me;
use frame\stdlib\cash\route;

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
        $router = route::get();
        if ($router->getArg($name) === null) throw new HttpError(
            HttpError::NOT_FOUND, 
            'The '.$name.' url argument does not exist.'
        );
        return $router->getArg($name);
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

    /** @throws HttpError FORBIDDEN */
    public static function accessGroup(int $groupId)
    {
        static::access((int) user_me::get()->group_id === $groupId);
    }

    /** 
     * @throws HttpError FORBIDDEN
     * @throws \Exception if there is no such module.
     * @throws \Exception if there is no such module rights.
     */
    public static function accessRight(string $module, string $right, ...$args)
    {
        if (!my_rights::get($module)->can($right, ...$args)) 
            throw new HttpError(HttpError::FORBIDDEN);
    }

    /** 
     * @throws HttpError FORBIDDEN
     * @throws \Exception if there is no such module.
     * @throws \Exception if there is no such module rights.
     */
    public static function accessOneRight(string $module, array $rights)
    {
        if (!my_rights::get($module)->canOneOf($rights))
            throw new HttpError(HttpError::FORBIDDEN);
    }
}