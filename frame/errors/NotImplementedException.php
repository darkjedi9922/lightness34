<?php namespace frame\errors;

use frame\tools\logging\Logger;

class NotImplementedException extends \Exception implements LogLevel
{
    public function getLogLevel(): string
    {
        return Logger::WARNING;
    }
}