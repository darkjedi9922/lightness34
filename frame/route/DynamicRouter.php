<?php namespace frame\route;

abstract class DynamicRouter
{
    private $routerDriver;
    private $dynamicTag;

    public function __construct(string $dynamicTag)
    {
        $this->routerDriver = Router::getDriver();
        $this->dynamicTag = $dynamicTag;
    }

    public function findRealRoute(string $route): ?Route
    {
        $parsedRoute = $this->routerDriver->parseRoute($route);
        return $this->findRealRouteRecusive('', $parsedRoute->getPathParts(), 0, []);
    }

    private function findRealRouteRecusive(
        string $path,
        array $pathParts,
        int $partIndex,
        array $dynamicArgs
    ): ?Route {
        $result = null;
        $fullPath = ($path === '' ? '' : "$path/");

        if ($partIndex < count($pathParts) - 1) {
            $delimiter = ($path === '' ? '' : '/');
            // Сначала пытаемся рекурсивно найти точный путь.
            if ($this->doesRealSubRouteExist($fullPath . $pathParts[$partIndex]))
                $result = $this->findRealRouteRecusive(
                    $path . $delimiter . $pathParts[$partIndex],
                    $pathParts,
                    $partIndex + 1,
                    $dynamicArgs
                );
            
            // Если там не нашли, ищем рекурсивно с динамическим именем.
            // Если и тут не найдем, $result останется null, его и вернем.
            if (!$result && $this->doesRealSubRouteExist($fullPath . $this->dynamicTag)) {
                $dynamicArgs[] = $pathParts[$partIndex];
                $result = $this->findRealRouteRecusive(
                    $path . $delimiter . $this->dynamicTag,
                    $pathParts,
                    $partIndex + 1,
                    $dynamicArgs
                );
            }
        } else {
            // Если это последний компонент пути - ищем именно конечный путь.
            // Он либо имеет точное название, ...
            $result = $this->findEndRealRoute(
                $fullPath . $pathParts[$partIndex],
                $dynamicArgs
            );
            if (!$result) {
                // ... либо динамическое.
                $dynamicArgs[] = $pathParts[$partIndex];
                $result = $this->findEndRealRoute(
                    "$fullPath{$this->dynamicTag}",
                    $dynamicArgs
                );
            }
        }

        return $result;
    }

    private function findEndRealRoute(string $route, array $dynamicArgs): ?Route
    {
        if (!$this->doesRealEndRouteExist($route)) return null;
        $result = $this->routerDriver->parseRoute($route);
        $result->args = $dynamicArgs;
        return $result;
    }

    protected abstract function doesRealSubRouteExist(string $subRoute): bool;
    protected abstract function doesRealEndRouteExist(string $endRoute): bool;
}