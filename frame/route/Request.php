<?php namespace frame\route;

abstract class Request extends \frame\core\Driver
{
    /**
     * Возвращает строку запроса, которая была запрошена.
     */
    public abstract function getCurrentRequest(): string;

    /**
     * Строка предыдущего запроса или NULL, если его не существует.
     */
    public abstract function getPreviousRequest(): ?string;

    public abstract function isAjax(): bool;
}