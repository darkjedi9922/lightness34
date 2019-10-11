<?php namespace frame\auth;

use frame\tools\transmitters\CookieTransmitter;

class Auth
{
    private $transmitter;

    public function __construct()
    {
        $this->transmitter = new CookieTransmitter;
    }

    public function login(string $key)
    {
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
        return $this->isLogged() ? $this->transmitter->getData('sid') : null;
    }
}