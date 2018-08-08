<?php namespace frame\tools\transmitters;

class SessionTransmitter extends DataTransmitter
{
    /**
     * О конструкторе сказать нечего
     */
    public function __construct()
    {
        session_start_once();
    }

    /**
     * {@inheritDoc}
     */
    public function setData($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getData($name)
    {
        if (!$this->isSetData($name)) throw new \Exception('The '.$name.' is not set.');
        return $_SESSION[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function isSetData($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function removeData($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $_SESSION;
    }
}