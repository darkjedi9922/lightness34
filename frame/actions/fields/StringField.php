<?php namespace frame\actions\fields;

class StringField extends BaseField
{
    public function __construct(string $value)
    {
        parent::__construct($value);
    }

    public function get(): string
    {
        return parent::get();
    }

    public function isEmpty(): bool
    {
        return $this->get() === '';
    }

    public function isTooLong(int $maxLength): bool
    {
        return strlen($this->get()) > $maxLength;
    }

    public function isTooShort(int $minLength): bool
    {
        return strlen($this->get()) < $minLength;
    }

    public function isRegExp(string $pattern): bool
    {
        return preg_match($pattern, $this->get());
    }
}