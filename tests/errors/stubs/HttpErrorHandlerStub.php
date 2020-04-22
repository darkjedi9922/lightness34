<?php namespace tests\errors\stubs;

use frame\errors\handlers\ErrorHandler;
use frame\errors\handlers\ErrorPage;

class HttpErrorHandlerStub implements ErrorHandler
{
    public function handle($error)
    {
        $errorPage = new ErrorPage('error', $error);
        $errorPage->show();
    }
}