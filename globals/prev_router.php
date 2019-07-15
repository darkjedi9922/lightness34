<?php namespace globals;

use frame\tools\GlobalValue;
use frame\route\Request;
use frame\route\Router;

/**
 * Возвращает роутер предыдущего запроса. Предыдущий запрос всегда одинаковый 
 * для всех роутеров (на предпредыдущий через предыдущий не попадешь). Если 
 * предыдущего запроса нет - вернет null.
 */
class prev_router extends GlobalValue
{
    public static function get(): ?Router
    {
        return parent::get();
    }

    public static function create(): ?Router
    {
        if (Request::hasReferer()) return new Router(Request::getReferer());
        else return null;
    }
}