<?php namespace frame\actions\fields;

use function lightlib\encode_specials;

class StringField extends \frame\actions\ActionField
{
    public function __construct(string $value)
    {
        parent::__construct(encode_specials($value));
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
        return  mb_strlen($this->get()) > $maxLength;
    }

    public function isTooShort(int $minLength): bool
    {
        return mb_strlen($this->get()) < $minLength;
    }

    public function isRegExp(string $pattern): bool
    {
        return preg_match($pattern, $this->get());
    }
}