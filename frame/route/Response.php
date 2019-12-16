<?php namespace frame\route;

use function lightlib\ob_end_clean_all;

class Response
{
    /**
     * После выполнения скрипт завершается.
     */
    public static function setUrl(string $url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * После выполнения скрипт завершается.
     */
    public static function setText(string $text)
    {
        ob_end_clean_all();
        echo $text;
        exit;
    }

    public static function setCode(int $code)
    {
        http_response_code($code);
    }

    public static function getCode(): int
    {
        return http_response_code();
    }
}