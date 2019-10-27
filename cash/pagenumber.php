<?php namespace cash;

use frame\Core;
use frame\tools\Cash;

/**
 * Номер страницы по счету в списке. Определяется get параметром "p".
 * Если его нет, то всегда равен 1.
 */
class pagenumber extends Cash
{
    public static function get(): int
    {
        return self::cash('p', function() {
            $p = Core::$app->router->getArg('p');
            if ($p === null || $p === '' || $p <= 0) return 1;
            else return $p;
        });
    }
}