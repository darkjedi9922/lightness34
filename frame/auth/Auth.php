<?php namespace frame\auth;

use frame\tools\transmitters\CookieTransmitter;

class Auth
{
    private $transmitter;

    public function __construct()
    {
        $this->transmitter = new CookieTransmitter;
    }

    public function login()
    {
        $this->transmitter->setData('sid', 1);
    }

    public function logout()
    {
        $this->transmitter->removeData('sid', 1);
    }

    public function isLogged(): bool
    {
        return $this->transmitter->isSetData('sid');
    }
}