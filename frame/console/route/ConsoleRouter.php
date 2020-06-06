<?php namespace frame\console\route;

use frame\route\Route;
use frame\route\Router;

class ConsoleRouter extends Router
{
    public function parseRoute(string $route): Route
    {
        $routeParts = explode(' ', $route);
        $result = new Route($route, implode('/', $routeParts), $routeParts, []);
        return $result;
    }

    public function makeRoute($route, array $newArgs = []): string
    {
        if (is_string($route)) return $route;
        return $route->url;
    }
}