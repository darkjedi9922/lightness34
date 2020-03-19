<?php namespace engine\users\actions\fields;

use frame\actions\fields\StringField;
use frame\stdlib\cash\config;
use engine\users\User;

class UserLogin extends StringField
{
    private $config;

    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->config = config::get('users');
    }

    public function isTooLongByConfig(): bool
    {
        return $this->isTooLong($this->config->{'login.max_length'});
    }

    public function isIncorrect(): bool
    {
        return $this->isRegExp('/[^a-zA-Z0-9_]/');
    }

    public function isTaken(): bool
    {
        return User::select(['login' => $this->get()]) !== null;
    }
}