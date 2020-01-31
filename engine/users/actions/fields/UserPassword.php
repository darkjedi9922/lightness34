<?php namespace engine\users\actions\fields;

use frame\actions\fields\PasswordField;
use frame\cash\config;

class UserPassword extends PasswordField
{
    private $config;

    public function __construct(string $value)
    {
        parent::__construct($value);    
        $this->config = config::get('users');
    }

    public function isTooLongByConfig(): bool
    {
        return $this->isTooLong($this->config->{'password.max_length'});
    }

    public function isIncorrect(): bool
    {
        return $this->isRegExp('/[^a-zA-Z0-9]/');
    }
}