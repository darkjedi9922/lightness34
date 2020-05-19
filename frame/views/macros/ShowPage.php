<?php namespace frame\views\macros;

use frame\route\HttpError;
use frame\route\Router;
use frame\views\ViewRouter;

class ShowPage extends \frame\events\Macro
{
    public function exec(...$args)
    {
        $router = Router::getDriver()->getCurrentRoute();
        $page = ViewRouter::getDriver()->findPage($router);
        if ($page) $page->show();
        else throw new HttpError(
            404, 'Page ' . $router->pagename . ' does not exist.'
        );
    }
}