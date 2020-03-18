<?php namespace frame\route;

abstract class Request extends \frame\core\Driver
{
    /**
     * Возвращает url, который был запрошен (откуда бы ни было)
     */
    public abstract function getRequest(): string;

    /**
     * Предыдущий url.
     * @throws \Exception если предыдущего url не существует
     */
    public abstract function getReferer(): string;
    
    public abstract function hasReferer(): bool;

    public abstract function isAjax(): bool;
}