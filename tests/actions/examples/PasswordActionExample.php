<?php namespace tests\actions\examples;

use frame\actions\ActionBody;

class PasswordActionExample extends ActionBody
{
    public function listPost(): array
    {
        return [
            'login' => [self::POST_TEXT, 'A simple text'],
            'password' => [self::POST_PASSWORD, 'A password']
        ];
    }

    public function succeed(array $post, array $files) { }
}