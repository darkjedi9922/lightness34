<?php namespace frame\route;

use frame\core\Driver;
use frame\route\Request;
use frame\cash\StaticCashStorage;

abstract class Router extends Driver
{
    public function getCurrentRoute(): Route
    {
        return StaticCashStorage::getDriver()->cash('current-router', function() {
            $request = Request::getDriver()->getRequest();
            return $this->parseRoute($request);
        });
    }

    public function getPreviousRoute(): ?Route
    {
        return StaticCashStorage::getDriver()->cash('prev-router', function() {
            $request = Request::getDriver();
            if ($request->hasReferer())
                return $this->parseRoute($request->getReferer());
            else return null;
        });
    }

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