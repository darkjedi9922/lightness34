<?php namespace frame\errors;

class HttpError extends \Exception
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
}