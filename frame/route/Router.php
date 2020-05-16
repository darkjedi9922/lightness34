<?php namespace frame\route;

use frame\core\Driver;

abstract class Router extends Driver
{
    public abstract function parseRoute(string $route): Route;
    
    /**
     * Преобразует url в тот же url с обновленными get параметрами
     * 
     * @param string|Route $route
     * @param array $newGet Новые значения get параметров. Чтобы удалить
     * существующий параметр, нужно присвоить ему значение null
     */
    public abstract function makeRoute($route, array $newArgs = []): string;
}