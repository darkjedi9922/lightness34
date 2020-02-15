<?php namespace frame\route;

use frame\core\Core;

use function lightlib\ob_end_clean_all;

class Response
{
    /**
     * Выбрасывается прямо перед полным завершением скрипта, в случае такого запроса.
     * (То есть только тогда, когда кто-то вызвал finish(), а он не вызывается всегда
     * при естественном завершении приложения).
     * 
     * Повторно не выбрасывается. (Во время обработки корректного завершения,
     * например, при вызове finish() это событие не будет снова выброшено, чтобы не
     * войти в рекурсию).
     */
    const EVENT_FINISH = 'response-force-finish';

    private static $redirect = null;
    private static $finish = false;

    /**
     * После выполнения скрипт завершается.
     */
    public static function setUrl(string $url)
    {
        self::$redirect = $url;
        header('Location: ' . $url);
        self::finish();
    }

    public static function getUrl(): ?string
    {
        return self::$redirect;
    }

    /**
     * После выполнения скрипт завершается.
     */
    public static function setText(string $text)
    {
        ob_end_clean_all();
        echo $text;
        self::finish();
    }

    public static function setCode(int $code)
    {
        http_response_code($code);
    }

    public static function getCode(): int
    {
        return http_response_code();
    }

    /**
     * Полностью завершает обработку запроса. Предпочительнее, чем exit, потому что
     * этот метод учитывает корректное завершение приложения, в отличии от exit.
     */
    public static function finish()
    {
        // Если этот метод уже был вызван, дабы не войти в рекурсию не будем
        // повторять все по новой.
        if (self::$finish) return;
       
        self::$finish = true;
        Core::$app->events->emit(self::EVENT_FINISH);
        exit;
    }
}