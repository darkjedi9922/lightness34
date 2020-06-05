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

    public function findPage(Route $route): ?Page
    {
        $pagename = $route->pagename;
        if (Page::find($pagename)) return new Page($pagename);
        $viewDynamicRouter = new ViewDynamicRouter(DynamicPage::class);
        $route = $viewDynamicRouter->findRealRoute($pagename);
        if ($route) {
            $view = new DynamicPage($route->url);
            $view->setMeta('$', $route->args);
            return $view;
        }
        return null;
    }
}