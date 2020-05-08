<?php namespace frame\core;

use frame\errors\handlers\ErrorHandler;
use function lightlib\remove_prefix;
use frame\errors\Errors;

class DriverAbstractClassHandler implements ErrorHandler
{
    public function handle($error)
    {
        $errorDriver = Errors::getDriver();
        $triggerMessage = 'Cannot instantiate abstract class ';
        if (strpos($error->getMessage(), $triggerMessage) === 0) {
            $className = remove_prefix($error->getMessage(), $triggerMessage);
            if (is_subclass_of($className, Driver::class)) {
                $backtrace = $error->getTrace();
                $caller = ($backtrace[1]['class'] ?? null) === Driver::class
                    ? $backtrace[1] : $backtrace[0];

                $callerFile = ltrim(remove_prefix($caller['file'], ROOT_DIR), '\\/');
                $callerLine = $caller['line'];

                $errorDriver->handleError(new DriverNotSetupException(
                    $className,
                    $callerFile,
                    $callerLine,
                    $error
                ));
            }
        }
    }
}