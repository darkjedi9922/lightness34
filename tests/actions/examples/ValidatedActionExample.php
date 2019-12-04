<?php namespace tests\actions\examples;

use frame\actions\Action;

class ValidatedActionExample extends Action
{
    const E_INVALID = 1;

    public function listPost(): array
    {
        return [
            'name' => [self::POST_TEXT, 'A name must not begin from _ symbol']
        ];
    }

    protected function validate(array $post, array $files): array
    {
        if ($post['name'][0] === '_') return [self::E_INVALID];
        else return [];
    }

    protected function succeed(array $post, array $files)
    {
        // No implementaion is here.
    }
}