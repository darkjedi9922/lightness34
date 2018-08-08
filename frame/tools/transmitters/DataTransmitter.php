<?php namespace frame\tools\transmitters;

abstract class DataTransmitter
{
    /**
     * @param string $name
     * @param string $value
     */
    public abstract function setData($name, $value);

    /** 
     * @param string $name
     * @return string
     * @throws \Exception Если заданное значение не существует 
     */
    public abstract function getData($name);

    /**
     * @param string $name
     * @return bool
     */
    public abstract function isSetData($name);

    /**
     * @param string $name
     */
    public abstract function removeData($name);

    /**
     * @return array
     */
    public abstract function toArray();

    /** 
     * Аналогично $obj->getData('name').
     * Использование: $obj->name
     * 
     * @throws \Exception 
     */
    public function __get($name)
    {
        return $this->getData($name);
    }
}