<?php namespace tests\route\examples;

use frame\route\DynamicRouter;

class DynamicRouterMock extends DynamicRouter
{
    private $routes = [];

    public function __construct(string $dynamicTag, array $routes)
    {
        parent::__construct($dynamicTag);
        $this->routes = $routes;
    }

    protected function doesRealSubRouteExist(string $subRoute): bool
    {
        foreach ($this->routes as $route) {
            if (strpos($route, $subRoute) === 0) return true;
        }
        return false;
    }

    protected function doesRealEndRouteExist(string $endRoute): bool
    {
        return in_array($endRoute, $this->routes);
    }
}