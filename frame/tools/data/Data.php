<?php namespace frame\tools\data;

interface Data
{
    /**
     * @param mixed|array $name
     * @return mixed|null
     */
    public function get($name);

    /**
     * @param mixed|array $name
     * @param mixed $value
     */
    public function set($name, $value);

    /**
     * @param mixed|array $name
     * @return bool
     */
    public function isset($name);

    /**
     * @param mixed $name
     * @return mixed|null
     */
    public function __get($name);

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value);

    /**
     * @param mixed $name
     * @return bool
     */
    public function __isset($name);

    /**
     * @param array $data
     */
    public function setData($data);

    /**
     * @return array
     */
    public function getData();
}