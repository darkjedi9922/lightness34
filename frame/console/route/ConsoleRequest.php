<?php namespace frame\console\route;

use frame\route\Request;

class ConsoleRequest extends Request
{
    public function getCurrentRequest(): string
    {
        return implode(' ', array_slice($_SERVER['argv'], 1));
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