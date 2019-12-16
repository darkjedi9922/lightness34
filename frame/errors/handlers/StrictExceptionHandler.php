<?php namespace frame\errors\handlers;

use frame\Core;
use frame\route\Response;
use frame\errors\HttpError;

class StrictExceptionHandler implements ErrorHandler
{
    public function handle($error)
    {
        $logging = Core::$app->config->{'log.enabled'};
        echo 'Error has occured but ' . $error->getMessage() . ($logging ? '. See more in the log.' : '');
        Response::setCode(HttpError::INTERNAL_SERVER_ERROR);
    }
}