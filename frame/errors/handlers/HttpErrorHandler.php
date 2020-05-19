<?php namespace frame\errors\handlers;

use frame\route\Response;
use frame\config\ConfigRouter;

class HttpErrorHandler implements ErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle($error)
    {
        $code = $error->getCode();
        $page = ConfigRouter::getDriver()->findConfig('core')->{"errors.$code.page"};
        Response::getDriver()->setCode($code);
        if (ErrorPage::find($page)) {
            $view = new ErrorPage($page, $error);
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