<?php namespace frame\errors\handlers;

use frame\route\Response;
use frame\route\HttpError;
use frame\config\ConfigRouter;

class StrictExceptionHandler implements ErrorHandler
{
    public function handle($error)
    {
        $logging = ConfigRouter::getDriver()->findConfig('core')->{'log.enabled'};
        echo 'Error has occured but ' . $error->getMessage() . ($logging ? '. See more in the log.' : '');
        Response::getDriver()->setCode(HttpError::INTERNAL_SERVER_ERROR);
    }
}