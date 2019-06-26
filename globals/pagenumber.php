<?php

/**
 * Номер страницы по счету в списке. Определяется get параметром "p".
 * Если его нет, то всегда равен 1.
 * 
 * @return int
 */

use frame\Core;

$p = Core::$app->router->getArg('p');
if ($p === null || $p === '' || $p <= 0) return 1;
else return (int)$p;