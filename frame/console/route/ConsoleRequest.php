<?php namespace frame\console\route;

use Exception;
use frame\route\Request;

class ConsoleRequest extends Request
{
    public function getCurrentRequest(): string
    {
        return implode(' ', $_SERVER['argv']);
    }

    public function getPreviousRequest(): ?string
    {
        return null;
    }

    public function isAjax(): bool
    {
        return false;
    }
}