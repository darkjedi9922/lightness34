<?php namespace engine\users\actions\fields;

class EmailField extends StringField
{
    public function isIncorrect(): bool
    {
        $pattern = '/^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$/';
        return !$this->isRegExp($pattern);
    }
}