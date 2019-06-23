<?php namespace frame\tools;

use function lightlib\session_start_once;
use frame\tools\transmitters\CookieTransmitter;

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
        $cookies = new CookieTransmitter(60*60*24*7*365);
        if (!$cookies->isSetData('cid')) {
            srand(time());
            $id = rand();
            $cookies->setData('cid', $id);
            return $id;
        }
        return $cookies->cid;
    }
    public static function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}