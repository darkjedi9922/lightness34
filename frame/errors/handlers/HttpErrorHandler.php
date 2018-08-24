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
        (new Page($error->getCode()))->show();
    }
}