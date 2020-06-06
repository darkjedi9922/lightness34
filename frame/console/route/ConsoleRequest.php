<?php namespace frame\console\route;

use Exception;
use frame\route\Request;

class ConsoleRequest extends Request
{
    public function getRequest(): string
    {
        return implode(' ', $_SERVER['argv']);
    }

    public function getReferer(): string
    {
        throw new Exception('Previous request does not exist');
        return '';
    }

    public function hasReferer(): bool
    {
        return false;
    }

    public function isAjax(): bool
    {
        return false;
    }
}