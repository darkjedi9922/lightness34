<?php namespace frame\errors;

use frame\tools\Logger;

class HttpError extends \Exception implements LogLevel
{
    const OK                    = 200;
    const BAD_REQUEST           = 400;
    const FORBIDDEN             = 403;
    const NOT_FOUND             = 404;
    const INTERNAL_SERVER_ERROR = 500;

    public function __construct(
        int $code,
        string $message = '',
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getLogLevel(): string
    {
        $type = (int) ($this->code / 100);
        switch ($type) {
            case 1: return Logger::INFO;
            case 2: return Logger::INFO;
            case 3: return Logger::INFO;
            case 4: return Logger::NOTICE;
            case 5: return Logger::ERROR;
        }
    }
}