<?php namespace frame\errors\handlers;

use frame\route\Response;
use frame\errors\HttpError;
use frame\stdlib\cash\config;

class StrictExceptionHandler implements ErrorHandler
{
    public function handle($error)
    {
        $logging = config::get('core')->{'log.enabled'};
        echo 'Error has occured but ' . $error->getMessage() . ($logging ? '. See more in the log.' : '');
        Response::get()->setCode(HttpError::INTERNAL_SERVER_ERROR);
    }
}