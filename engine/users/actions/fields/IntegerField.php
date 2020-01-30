<?php namespace engine\users\actions\fields;

class IntegerField
{
    protected $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function __toString(): int
    {
        return $this->value;
    }
}