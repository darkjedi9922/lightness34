<?php namespace engine\admin;

use frame\http\SessionTransmitter;
use frame\http\CookieTransmitter;

/**
 * В качестве ключа используется admin.
 */
class Auth
{
    private $cookies;
    private $transmitter;

    public function __construct()
    {
        $this->transmitter = new SessionTransmitter;
        $this->cookies = new CookieTransmitter;
    }

    public function login(string $key)
    {
        $this->transmitter->setData('admin', $key);
        $this->cookies->setData('admin_logined', 1);
    }

    public function logout()
    {
        $this->transmitter->removeData('admin');
        $this->cookies->removeData('admin_logined');
    }

    public function isLogged()
    {
        return $this->transmitter->isSetData('admin');
    }

    public function getKey(): ?string
    {
        if (!$this->isLogged()) return null;
        return $this->transmitter->getData('admin');
    }

    public function isTimeup(): bool
    {
        return (!$this->isLogged() && $this->cookies->isSetData('admin_logined'));
    }

    public function clearTimeupFlag()
    {
        if ($this->isTimeup()) $this->cookies->removeData('admin_logined');
    }
}