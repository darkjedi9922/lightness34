<?php namespace frame\route;

class Request
{
    /**
     * Предстоставляет запрос, который явно виден в адресной строке
     */
    public static function getUrl() : string
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Обычно идентичен url, но не всегда.
     * Предоставляет запрос, который был запрошен (откуда бы ни было)
     */
    public static function getRequest() : string
    {
        // INFO: на хостинге может не быть REDIRECT_URL.
        // Очевидно, это был плохой хостинг.
        return $_SERVER['REDIRECT_URL'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
    }

    /**
     * Предыдущий url.
     * 
     * @throws \Exception если предыдущего url не существует
     */
    public static function getReferer() : string
    {
        if (!self::hasReferer()) throw new \Exception('The referer is not exist');
        return $_SERVER['HTTP_REFERER'];
    }
    
    public static function hasReferer() : bool
    {
        return isset($_SERVER['HTTP_REFERER']);
    }

    public static function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? null) == 'XMLHttpRequest';
    }
}