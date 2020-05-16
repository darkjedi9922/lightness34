<?php namespace frame\stdlib\cash;

use frame\cash\CashValue;
use frame\route\Request;
use frame\route\Router;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;

/**
 * Возвращает роутер предыдущего запроса. Предыдущий запрос всегда одинаковый 
 * для всех роутеров (на предпредыдущий через предыдущий не попадешь). Если 
 * предыдущего запроса нет - вернет null.
 */
class prev_route extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @return Route|null
     */
    public static function get()
    {
        return self::cash('prev-router', function() {
            if (Request::getDriver()->hasReferer()) 
                return Router::getDriver()->parseRoute(Request::getDriver()->getReferer());
            else return null;
        });
    }
}