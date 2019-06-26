<?php

/**
 * Не стоит использовать этот id в целях безопасности, он предназначен лишь для
 * идентификации клиентов.
 * 
 * @return string
 */

use frame\tools\transmitters\CookieTransmitter;

$cookies = new CookieTransmitter(60 * 60 * 24 * 7 * 365 * 10);
if (!$cookies->isSetData('cid')) {
    $newId = md5(self::getSessionId() . time());
    $cookies->setData('cid', $newId);
    return $newId;
}
return $cookies->cid;