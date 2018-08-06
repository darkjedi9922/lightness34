<?php namespace frame\http;

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
}