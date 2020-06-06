<?php namespace frame\stdlib\drivers\route;

class UrlRequest extends \frame\route\Request
{
    public function getCurrentRequest(): string
    {
        // INFO: на хостинге может не быть REDIRECT_URL.
        // Очевидно, это был плохой хостинг.
        return $_SERVER['REDIRECT_URL'] .
            ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
    }

    public function getPreviousRequest() : ?string
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }
    
    public function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? null) == 'XMLHttpRequest';
    }
}