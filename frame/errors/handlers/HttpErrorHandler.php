<?php namespace frame\errors\handlers;

use frame\Core;
use frame\views\Page;

class HttpErrorHandler implements ErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle($error)
    {
        $page = Core::$app->config->{'errors.' . $error->getCode() . '.page'};
        if (Page::find((string) $error->getCode())) {
            (new Page($error->getCode()))->show();
        } else {
            $defaultHandler = new DefaultErrorHandler;
            $defaultHandler->handle(new \Exception(
                "Notice: This error can be hidden in {$error->getCode()} page.",
                0, $error
            ));
        }
    }
}