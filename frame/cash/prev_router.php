<?php namespace frame\cash;

use frame\tools\Cash;
use frame\route\Request;
use frame\route\Router;

/**
 * Возвращает роутер предыдущего запроса. Предыдущий запрос всегда одинаковый 
 * для всех роутеров (на предпредыдущий через предыдущий не попадешь). Если 
 * предыдущего запроса нет - вернет null.
 */
class prev_router extends Cash
{
    public static function get(): ?Router
    {
        return self::cash('prev-router', function() {
            if (Request::get()->hasReferer()) return new Router(Request::get()->getReferer());
            else return null;
        });
    }
}