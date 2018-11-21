<?php namespace frame\tools;

use function lightlib\session_start_once;

class Client 
{
    public static function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'];
    }
    public static function getSessionId(): string
    {
        session_start_once();
        return session_id();
    }
    public static function getId(): string
    {
        return md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
    }
    public static function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}