<?php namespace frame\auth;

use frame\tools\transmitters\CookieTransmitter;

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