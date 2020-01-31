<?php namespace frame\actions\fields;

class PasswordField extends StringField
{
    public function canBeSaved(): bool
    {
        return false;
    }
}