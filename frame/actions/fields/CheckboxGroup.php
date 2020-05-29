<?php namespace frame\actions\fields;

use frame\actions\ActionField;

class CheckboxGroup extends ActionField
{
    public static function createDefault()
    {
        return new static([]);
    }

    public function __construct(array $value)
    {
        parent::__construct($value);
    }

    public function getCheckedValues(): array
    {
        return $this->get();
    }

    public function isValueChecked(string $value): bool
    {
        return in_array($value, $this->get());
    }
}