<?php namespace drivers\route;

class UrlRequest extends \frame\route\Request
{
    public function getRequest(): string
    {
        // INFO: на хостинге может не быть REDIRECT_URL.
        // Очевидно, это был плохой хостинг.
        return $_SERVER['REDIRECT_URL'] .
            ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
    }

    public function getReferer() : string
    {
        if (!$this->hasReferer()) throw new \Exception('The referer is not exist');
        return $_SERVER['HTTP_REFERER'];
    }
    
    public function hasReferer() : bool
    {
        return isset($_SERVER['HTTP_REFERER']);
    }

    public function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? null) == 'XMLHttpRequest';
    }
}