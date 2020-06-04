<?php namespace frame\views;

use frame\route\DynamicRouter;
use frame\views\ViewRouter;
use frame\views\DynamicPage;

class ViewDynamicRouter extends DynamicRouter
{
    private $pagesDir;

    public function __construct()
    {
        parent::__construct('$meta');
        $baseFolder = ViewRouter::getDriver()->getBaseFolder();
        $this->pagesDir = $baseFolder . '/' . DynamicPage::getNamespace();
    }

    protected function doesRealSubRouteExist(string $subRoute): bool
    {
        return is_dir($this->pagesDir . "/$subRoute");
    }

    protected function doesRealEndRouteExist(string $endRoute): bool
    {
        return DynamicPage::find($endRoute) !== null;
    }
}