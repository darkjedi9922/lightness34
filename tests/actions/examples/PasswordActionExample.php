<?php namespace tests\actions\examples;

use frame\actions\ActionBody;
use frame\actions\fields\PasswordField;
use frame\actions\fields\StringField;

class PasswordActionExample extends ActionBody
{
    public function listPost(): array
    {
        return [
            'login' => StringField::class,
            'password' => PasswordField::class
        ];
    }

    public function succeed(array $post, array $files) { }
}