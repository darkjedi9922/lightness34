<?php namespace frame\actions\fields;

use frame\actions\ActionField;

class CheckboxField extends ActionField
{
    public static function createDefault()
    {
        return new static(null);
    }

    public function __construct(?string $value)
    {
        parent::__construct($value);
    }

    public function isChecked(): bool
    {
        return !in_array($this->get(), [null, '0', 0, 'off'], true);
    }
}