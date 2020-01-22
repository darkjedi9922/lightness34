<?php namespace frame\database;

use Throwable;

class QueryException extends \Exception
{
    private $query;

    public function __construct(
        string $query,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->query = $query;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}