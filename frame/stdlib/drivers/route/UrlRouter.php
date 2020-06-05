<?php namespace frame\stdlib\drivers\route;

use frame\route\Router;
use frame\route\Route;

class UrlRouter extends Router
{
    public function parseRoute(string $route): Route
    {
        $pagename = trim(parse_url($route, PHP_URL_PATH), '/');
        $pathElements = explode('/', $pagename);
        $query = parse_url($route, PHP_URL_QUERY);
        parse_str($query, $args);
        return new Route($route, $pagename, $pathElements, $args);
    }

    public function makeRoute($route, array $newArgs = []): string
    {
        if (!is_string($route)) $route = $route->url;
        if (empty($newArgs)) return $route;
        else {
            $route = trim($route, '=&');
            $query = parse_url($route, PHP_URL_QUERY);
            parse_str($query, $args);
            $newArgs = array_merge($args, $newArgs);
            $newQuery = http_build_query($newArgs);
            $oldQuery = $query;
            if (!empty($oldQuery)) return str_replace($oldQuery, $newQuery, $route);
            else return $route . '?' . $newQuery;
        }
    }
}