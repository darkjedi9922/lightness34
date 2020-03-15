<?php namespace frame\errors\handlers;

use frame\route\Response;
use frame\errors\HttpError;
use frame\cash\config;

class StrictExceptionHandler implements ErrorHandler
{
    public function handle($error)
    {
        $logging = config::get('core')->{'log.enabled'};
        echo 'Error has occured but ' . $error->getMessage() . ($logging ? '. See more in the log.' : '');
        Response::setCode(HttpError::INTERNAL_SERVER_ERROR);
    }
}