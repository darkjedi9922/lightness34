<?php namespace engine\users\actions\fields;

use frame\actions\fields\StringField;

class NameField extends StringField
{
    public function isIncorrect(): bool
    {
        return $this->isRegExp('/[^a-zA-ZА-Яа-я]/u');
    }
}