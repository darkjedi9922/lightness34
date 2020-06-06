<?php namespace frame\views;

use frame\core\Driver;
use frame\route\Route;
use frame\views\Page;

class ViewRouter extends Driver
{
    public function getBaseFolder(): string
    {
        return ROOT_DIR . '/views';
    }

    public function getPageClass(): string
    {
        return Page::class;
    }

    public function getDynamicPageClass(): string
    {
        return DynamicPage::class;
    }

    public function findPage(Route $route): ?Page
    {
        $pagename = $route->pagename;
        $pageClass = $this->getPageClass();
        if ($pageClass::find($pagename)) return new $pageClass($pagename);
        $dynamicPageClass = $this->getDynamicPageClass();
        $viewDynamicRouter = new ViewDynamicRouter($dynamicPageClass);
        $route = $viewDynamicRouter->findRealRoute($pagename);
        if ($route) {
            $view = new $dynamicPageClass($route->url);
            $view->setMeta('$', $route->args);
            return $view;
        }
        return null;
    }
}