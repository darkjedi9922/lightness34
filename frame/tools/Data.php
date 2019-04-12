<?php namespace frame\tools;

use function lightlib\array_get_value;
use function lightlib\array_set_value;
use function lightlib\array_isset_value;

class Data
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->setData($data);
    }

    /**
     * @param mixed|array $name
     * @return mixed
     */
    public function get($name)
    {
        return array_get_value($this->data, $name);
    }

    /**
     * @param mixed|array $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->data = array_set_value($this->data, $name, $value);
    }

    /**
     * @param mixed|array $name
     * @return bool
     */
    public function isset($name)
    {
        return array_isset_value($this->date, $name);
    }

    /**
     * @param mixed $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->isset($name);
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}