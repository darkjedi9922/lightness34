<?php namespace frame\stdlib\cash;

use frame\cash\CashValue;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;

/**
 * Номер страницы по счету в списке. Определяется get параметром "p".
 * Если его нет, то всегда равен 1.
 */
class pagenumber extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @param bool $previous Возвращает номер предыдущей страницы.
     * @return int
     */
    public static function get(bool $previous = false)
    {
        return self::cash("p$previous", function() use ($previous) {
            $router = router::get($previous);
            if (!$router) return 1;
            $p = $router->getArg('p');
            if (!$p || $p <= 0) return 1;
            else return $p;
        });
    }
}