<?php namespace globals;

use frame\Core;
use frame\tools\GlobalValue;

/**
 * Номер страницы по счету в списке. Определяется get параметром "p".
 * Если его нет, то всегда равен 1.
 */
class pagenumber extends GlobalValue
{
    public static function get(): int
    {
        return parent::get();
    }

    public static function create(): int
    {
        $p = Core::$app->router->getArg('p');
        if ($p === null || $p === '' || $p <= 0) return 1;
        else return (int) $p;
    }
}