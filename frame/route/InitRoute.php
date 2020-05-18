<?php namespace frame\route;

use frame\route\HttpError;
use frame\stdlib\cash\route;

class InitRoute
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
            'The ' . $name . ' url argument does not exist.'
        );
        return $router->getArg($name);
    }
}