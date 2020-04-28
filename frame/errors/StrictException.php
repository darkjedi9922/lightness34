<?php namespace frame\errors;

use frame\tools\Logger;

/**
 * Такие исключения должны игнорировать любые настройки о выводе ошибок
 * и выводится всегда сразу на месте возникновения.
 */
class StrictException extends \Exception implements LogLevel
{
    public function getLogLevel(): string
    {
        return Logger::EMERGENCY;
    }
}