<?php namespace frame\exceptions;

class HttpError extends \Exception
{
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;

    private $HEADERS = [
        403 => '403 Forbidden',
        404 => '404 Not Found'
    ];

    public function __construct(int $code, string $message = '', \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);   
    }
    /**
     * Для использования функцией header()
     */
    public function getHeader(): string
    {
        return 'HTTP/1.0 '.$this->HEADERS[$this->getCode()];
    }
}