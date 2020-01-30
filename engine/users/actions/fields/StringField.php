<?php namespace engine\users\actions\fields;

class StringField
{
    protected $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    public function isTooLong(int $maxLength): bool
    {
        return strlen($this->value) > $maxLength;
    }

    public function isRegExp(string $pattern): bool
    {
        return preg_match($pattern, $this->value);
    }
}