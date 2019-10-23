<?php namespace frame\tools;

use engine\users\cash\user_me;
use frame\errors\HttpError;
use frame\auth\Auth;
use frame\Core;
use frame\modules\UserRights;

class Init
{
    private static $cashedRights = [];

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

    /** 
     * @throws HttpError FORBIDDEN
     * @throws \Exception if there is not such module.
     * @throws \Exception if there is no such module rights.
     */
    public static function accessRight(string $module, string $right, $object = null)
    {
        if (!isset(self::$cashedRights[$module])) {
            $moduleInstance = Core::$app->getModule($module);
            if (!$moduleInstance) 
                throw new \Exception("There is not the module $module.");

            $desc = $moduleInstance->createRightsDescription();
            if (!$desc) throw new \Exception(
                "There is no module rights of $module module.");

            self::$cashedRights[$module] = 
                new UserRights($desc, $moduleInstance->getId(), user_me::get());
        }

        if (!self::$cashedRights[$module]->can($right, $object)) 
            throw new HttpError(HttpError::FORBIDDEN);
    }
}