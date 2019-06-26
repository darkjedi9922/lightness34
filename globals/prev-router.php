<?php

/**
 * Возвращает роутер предыдущего запроса. Предыдущий запрос всегда одинаковый 
 * для всех роутеров (на предпредыдущий через предыдущий не попадешь). Если 
 * предыдущего запроса нет - вернет null.
 * 
 * @return Router|null
 */

use frame\route\Request;
use frame\route\Router;

if (Request::hasReferer()) return new Router(Request::getReferer());
else return null;