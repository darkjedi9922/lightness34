<?php namespace frame\tools\transmitters;

class CookieTransmitter extends DataTransmitter
{
    /**
     * @var int Продолжительность жизни печенек в секундах
     */
    private $expire = 0;

    /**
     * @var int $seconds_expire Продолжительность жизни печенек в секундах
     */
    public function __construct($seconds_expire = 0)
    {
        $this->setExpire($seconds_expire);
    }

    /**
     * @var int $seconds_expire Продолжительность жизни печенек в секундах
     */
    public function setExpire($seconds_expire)
    {
        if ($seconds_expire != 0) $this->expire = time() + $seconds_expire;
        else $this->expire = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function setData($name, $value)
    {
        setcookie($name, $value, $this->expire, '/');
    }

    /**
     * {@inheritDoc}
     */
    public function getData($name)
    {
        if (!$this->isSetData($name)) throw new \Exception('The '.$name.' is not set.');
        return $_COOKIE[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function isSetData($name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function removeData($name)
    {
        setcookie($name, '', time() - 3600, '/');
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $_COOKIE;
    }
}