<?php namespace globals;

use frame\tools\GlobalValue;
use frame\tools\transmitters\CookieTransmitter;

/**
 * Не стоит использовать этот id в целях безопасности, он предназначен лишь для
 * идентификации клиентов.
 */
class client_id extends GlobalValue
{
    public static function get(): string {
        return parent::get();
    }

    public static function create() {
        $cookies = new CookieTransmitter(60 * 60 * 24 * 7 * 365 * 10);
        if (!$cookies->isSetData('cid')) {
            $newId = md5(self::getSessionId() . time());
            $cookies->setData('cid', $newId);
            return $newId;
        }
        return $cookies->cid;
    }
}