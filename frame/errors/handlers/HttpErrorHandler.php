<?php namespace frame\errors\handlers;

use frame\Core;
use frame\views\Page;
use frame\route\Response;

class HttpErrorHandler implements ErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle($error)
    {
        $code = $error->getCode();
        $page = Core::$app->config->{"errors.$code.page"};
        Response::setCode($code);
        if (Page::find($page)) (new Page($page))->show();
        else {
            $defaultHandler = new DefaultErrorHandler;
            $defaultHandler->handle(new \Exception(
                "Notice: This error can be hidden in $code page.",
                0, $error
            ));
        }
    }
}