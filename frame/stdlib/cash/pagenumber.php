<?php namespace frame\stdlib\cash;

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
        return self::cash("p$previous", function() use ($previous) {
            $router = router::get($previous);
            if (!$router) return 1;
            $p = $router->getArg('p');
            if (!$p || $p <= 0) return 1;
            else return $p;
        });
    }
}