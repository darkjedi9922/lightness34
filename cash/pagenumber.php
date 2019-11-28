<?php namespace cash;

use frame\Core;
use frame\tools\Cash;

/**
 * Номер страницы по счету в списке. Определяется get параметром "p".
 * Если его нет, то всегда равен 1.
 */
class pagenumber extends Cash
{
    /**
     * @param bool $previous Возвращает номер предыдущей страницы.
     */
    public static function get(bool $previous = false): int
    {
        return self::cash('p', function() use ($previous) {
            $router = $previous ? prev_router::get() : Core::$app->router;
            if (!$router) return 1;
            $p = $router->getArg('p');
            if (!$p || $p <= 0) return 1;
            else return $p;
        });
    }
}