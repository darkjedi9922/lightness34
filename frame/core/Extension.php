<?php namespace frame\core;

abstract class Extension
{
    private $engine;

    public function __construct($engine)
    {
        $this->engine = $engine;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->engine->$name(...$arguments);
    }

    public function __get(string $name)
    {
        return $this->engine->$name;
    }
}
