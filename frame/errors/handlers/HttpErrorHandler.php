<?php namespace frame\errors\handlers;

use frame\core\Core;
use frame\route\Response;
use frame\views\ErrorPage;

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
        if (ErrorPage::find($page)) (new ErrorPage($page))->show();
        else {
            $defaultHandler = new DefaultErrorHandler;
            $defaultHandler->handle(new \Exception(
                "Notice: This error can be hidden in $code page.",
                0, $error
            ));
        }
    }
}