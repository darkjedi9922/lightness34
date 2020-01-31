<?php namespace frame\actions\fields;

class IntegerField extends BaseField
{
    public function __construct(int $value)
    {
        parent::__construct($value);
    }

    public function get(): int
    {
        return parent::get();
    }
}