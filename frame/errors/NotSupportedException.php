<?php namespace frame\errors;

use frame\tools\Logger;

class NotSupportedException extends \Exception implements LogLevel
{
    public function getLogLevel(): string
    {
        return Logger::WARNING;
    }
}