<?php namespace frame\console\route;

use frame\route\Route;
use frame\route\Router;

class ConsoleRouter extends Router
{
    public function parseRoute(string $request): Route
    {
        $routeParts = array_slice(explode(' ', $request), 1);
        $route = implode(' ', $routeParts);
        $result = new Route($route, $route, $routeParts, []);
        return $result;
    }

    public function makeRoute($route, array $newArgs = []): string
    {
        if (is_string($route)) return $route;
        return $route->url;
    }
}