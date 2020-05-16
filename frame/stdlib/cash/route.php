<?php namespace frame\stdlib\cash;

use frame\route\Request;
use frame\route\Router;
use frame\cash\CashValue;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;

/**
 * Возвращает роутер текущего запроса.
 */
class route extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return \frame\route\Route
     */
    public static function get()
    {
        return self::cash('current-router', function() {
            $request = Request::getDriver()->getRequest();
            return Router::getDriver()->parseRoute($request);
        });
    }
}