<?php namespace frame\errors\handlers;

use Throwable;

class ErrorPage extends \frame\views\Page
{
    private $error;

    public static function getNamespace(): string
    {
        return 'errors';
    }

    public function __construct(
        string $name,
        Throwable $error,
        ?string $layout = null
    ) {
        parent::__construct($name, $layout);
        $this->error = $error;
    }

    public function getError(): Throwable
    {
        return $this->error;
    }
}