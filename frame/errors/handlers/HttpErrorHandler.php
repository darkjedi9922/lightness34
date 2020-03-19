<?php namespace frame\errors\handlers;

use frame\route\Response;
use frame\stdlib\cash\config;

class HttpErrorHandler implements ErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle($error)
    {
        $code = $error->getCode();
        $page = config::get('core')->{"errors.$code.page"};
        Response::get()->setCode($code);
        if (ErrorPage::find($page)) {
            $view = new ErrorPage($page);
            $view->show();
        } else {
            $defaultHandler = new DefaultErrorHandler;
            $defaultHandler->handle(new \Exception(
                "Notice: This error can be hidden in $code page.",
                0, $error
            ));
        }
    }
}