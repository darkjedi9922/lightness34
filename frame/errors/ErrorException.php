<?php namespace frame\errors;

use frame\tools\Logger;

class ErrorException extends \ErrorException implements LogLevel
{
    /**
     * Ключи являются числовыми константами, а значения - именами констант
     */
    const LEVEL_ERRORS = [
        E_ERROR => Logger::CRITICAL,
        E_WARNING => Logger::WARNING,
        E_PARSE => Logger::CRITICAL,
        E_NOTICE => Logger::NOTICE,
        E_CORE_ERROR => Logger::CRITICAL,
        E_CORE_WARNING => Logger::WARNING,
        E_COMPILE_ERROR => Logger::CRITICAL,
        E_COMPILE_WARNING => Logger::WARNING,
        E_USER_ERROR => Logger::ERROR,
        E_USER_WARNING => Logger::WARNING,
        E_USER_NOTICE => Logger::NOTICE,
        E_STRICT => Logger::NOTICE,
        E_RECOVERABLE_ERROR => Logger::ERROR,
        E_DEPRECATED => Logger::NOTICE
    ];

    public function __construct(
        int $type,
        string $message,
        string $file,
        int $line,
        \Exception $previous = null
    ) {
        parent::__construct($message, 0, $type, $file, $line, $previous);
    }

    public function getLogLevel(): string
    {
        return static::LEVEL_ERRORS[$this->severity] ?? Logger::ERROR;
    }
}