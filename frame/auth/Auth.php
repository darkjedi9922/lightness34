<?php namespace frame\auth;

use frame\http\CookieTransmitter;

class Auth
{
    private $transmitter;

    public function __construct()
    {
        $this->transmitter = new CookieTransmitter;
    }

    public function login(string $key, bool $remember = false)
    {
        $key = $remember ? '1'.$key : '0'.$key;
        // If do remember then the expire is 10 years.
        $this->transmitter->setExpire($remember ? 60*60*24*30*12*10 : 0);
        $this->transmitter->setData('sid', $key);
    }

    public function logout()
    {
        $this->transmitter->removeData('sid');
    }

    public function isLogged(): bool
    {
        return $this->transmitter->isSetData('sid');
    }

    public function getKey(): ?string
    {
        if (!$this->isLogged()) return null;
        return substr($this->transmitter->getData('sid'), 1);
    }

    public function isRemembered(): bool
    {
        if (!$this->isLogged()) return false;
        return $this->transmitter->getData('sid')[0];
    }
}