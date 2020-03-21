<?php namespace frame\stdlib\tools\transmitters;

class StaticTransmitter extends \frame\tools\DataTransmitter
{
    /**
     * @var array Массив с данными
     */
    private $data = [];

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setData($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getData($name)
    {
        if (!$this->isSetData($name)) 
            throw new \Exception('The '.$name.' is not set.');
        return $this->data[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function isSetData($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function removeData($name)
    {
        unset($this->data[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->data;
    }
}