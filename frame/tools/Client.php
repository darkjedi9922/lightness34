<?php namespace frame\tools;

use function lightlib\session_start_once;
use frame\route\CookieTransmitter;
use function lightlib\stored;

class Client
{
    private static $storage = [];

    public static function isCli(): bool
    {
        if(defined('STDIN')) return true;
        return empty($_SERVER['REMOTE_ADDR'])
            && !isset($_SERVER['HTTP_USER_AGENT']) 
            && count($_SERVER['argv']) > 0;
    }

    public static function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'];
    }
    
    public static function getSessionId(): string
    {
        session_start_once();
        return session_id();
    }

    /**
     * Не стоит использовать этот id в целях безопасности, он предназначен лишь для
     * идентификации клиентов.
     */
    public static function getId(): string
    {
        return stored(self::$storage, 'id', function() {
            $cookies = new CookieTransmitter(60 * 60 * 24 * 7 * 365 * 10);
            if (!$cookies->isSetData('cid')) {
                $newId = md5(self::getSessionId() . time());
                $cookies->setData('cid', $newId);
                return $newId;
            }
            return $cookies->cid;
        });
    }
    
    public static function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}