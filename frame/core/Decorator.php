<?php namespace frame\core;

abstract class Decorator
{
    private $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->object->$name(...$arguments);
    }

    public function __get(string $name)
    {
        return $this->object->$name;
    }
}
