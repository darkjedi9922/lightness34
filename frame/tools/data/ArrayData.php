<?php namespace frame\tools\data;

use frame\tools\data\Data;

use function lightlib\array_get_value;
use function lightlib\array_set_value;
use function lightlib\array_isset_value;

class ArrayData implements Data
{
    private $data = [];

    public function __construct($data = []) {
        $this->setData($data);
    }

    public function get($name) {
        return array_get_value($this->data, $name);
    }

    public function set($name, $value) {
        $this->data = array_set_value($this->data, $name, $value);
    }

    public function isset($name) {
        return array_isset_value($this->data, $name);
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __set($name, $value) {
        $this->set($name, $value);
    }

    public function __isset($name) {
        return $this->isset($name);
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }
}