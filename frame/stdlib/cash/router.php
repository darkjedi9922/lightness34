<?php namespace frame\stdlib\cash;

use frame\route\Request;
use frame\route\Router as FrameRouter;
use frame\cash\CashValue;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;

/**
 * Возвращает роутер текущего запроса.
 */
class router extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return FrameRouter
     */
    public static function get()
    {
        return self::cash('current-router', function() {
           return new FrameRouter(Request::getDriver()->getRequest());
        });
    }
}