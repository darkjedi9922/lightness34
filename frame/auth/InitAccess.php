<?php namespace frame\auth;

use frame\auth\Auth;
use frame\route\HttpError;
use engine\users\User;

class InitAccess
{
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
        static::access((int) User::getMe()->group_id === $groupId);
    }

    /** 
     * @throws HttpError FORBIDDEN
     * @throws \Exception if there is no such module.
     * @throws \Exception if there is no such module rights.
     */
    public static function accessRight(string $module, string $right, ...$args)
    {
        if (!User::getMyRights($module)->can($right, ...$args)) 
            throw new HttpError(HttpError::FORBIDDEN);
    }

    /** 
     * @throws HttpError FORBIDDEN
     * @throws \Exception if there is no such module.
     * @throws \Exception if there is no such module rights.
     */
    public static function accessOneRight(string $module, array $rights)
    {
        if (!User::getMyRights($module)->canOneOf($rights))
            throw new HttpError(HttpError::FORBIDDEN);
    }
}