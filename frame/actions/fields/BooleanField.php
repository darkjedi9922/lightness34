<?php namespace frame\actions\fields;

class BooleanField extends BaseField
{
    public function createDefault()
    {
        return new static(false);
    }

    public function __construct($value)
    {
        if ($value === 'false') $value = false;
        parent::__construct((bool) $value);
    }

    public function get(): bool
    {
        return parent::get();
    }
}