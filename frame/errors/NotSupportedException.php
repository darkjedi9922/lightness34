<?php namespace frame\errors;

use frame\tools\logging\Logger;

class NotSupportedException extends \Exception implements LogLevel
{
    public function getLogLevel(): string
    {
        return Logger::WARNING;
    }
}