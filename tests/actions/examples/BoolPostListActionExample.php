<?php namespace tests\actions\examples;

use frame\actions\ActionBody;

class BoolPostListActionExample extends ActionBody
{
    public function listPost(): array
    {
        return [
            'checked' => self::POST_BOOL
        ];
    }

    public function succeed(array $post, array $files) {}
}