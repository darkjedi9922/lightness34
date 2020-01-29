<?php namespace tests\actions\examples;

use frame\actions\ActionBody;

class ValidatedActionExample extends ActionBody
{
    const E_INVALID = 1;

    public function listPost(): array
    {
        return [
            'name' => self::POST_TEXT // Must not begin from _ symbol to pass tests
        ];
    }

    public function validate(array $post, array $files): array
    {
        if ($post['name'][0] === '_') return [self::E_INVALID];
        else return [];
    }

    public function succeed(array $post, array $files)
    {
        // No implementaion is here.
    }
}