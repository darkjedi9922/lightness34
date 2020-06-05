<?php namespace frame\views;

use frame\route\DynamicRouter;
use frame\views\ViewRouter;

class ViewDynamicRouter extends DynamicRouter
{
    private $pagesDir;
    private $viewClass;

    public function __construct(string $viewClass)
    {
        parent::__construct('$meta');
        $baseFolder = ViewRouter::getDriver()->getBaseFolder();
        $this->pagesDir = $baseFolder . '/' . $viewClass::getNamespace();
        $this->viewClass = $viewClass;
    }

    protected function doesRealSubRouteExist(string $subRoute): bool
    {
        return is_dir($this->pagesDir . "/$subRoute");
    }

    protected function doesRealEndRouteExist(string $endRoute): bool
    {
        return $this->viewClass::find($endRoute) !== null;
    }
}