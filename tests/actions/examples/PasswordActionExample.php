<?php namespace tests\actions\examples;

use frame\actions\ActionBody;

class PasswordActionExample extends ActionBody
{
    public function listPost(): array
    {
        return [
            'login' => self::POST_TEXT,
            'password' => self::POST_PASSWORD
        ];
    }

    public function succeed(array $post, array $files) { }
}