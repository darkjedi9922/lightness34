<?php namespace frame\tools\transmitters;

class CookieTransmitter extends DataTransmitter
{
    /**
     * Чтобы cookie подействовали, нужно перезагрузить страницу.
     * Чтобы можно было использовать cookie сразу без перезагрузки, 
     * сделаем статический кеш.
     */
    private static $cash;

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
        if (!self::$cash) self::$cash = new StaticTransmitter;
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
        self::$cash->setData($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getData($name)
    {
        return $_COOKIE[$name] ?? self::$cash->getData($name);
    }

    /**
     * {@inheritDoc}
     */
    public function isSetData($name)
    {
        return isset($_COOKIE[$name]) || self::$cash->isSetData($name);
    }

    /**
     * {@inheritDoc}
     */
    public function removeData($name)
    {
        setcookie($name, '', time() - 3600, '/');
        self::$cash->removeData($name);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return array_merge($_COOKIE, self::$cash->toArray());
    }
}