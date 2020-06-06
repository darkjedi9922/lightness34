<?php namespace frame\route;

abstract class Request extends \frame\core\Driver
{
    /**
     * Возвращает строку запроса, которая была запрошена.
     */
    public abstract function getRequest(): string;

    /**
     * Предыдущий запрос.
     * @throws \Exception если предыдущего запроса не существует
     */
    public abstract function getReferer(): string;
    
    public abstract function hasReferer(): bool;

    public abstract function isAjax(): bool;
}