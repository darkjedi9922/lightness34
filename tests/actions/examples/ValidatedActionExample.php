<?php namespace tests\actions\examples;

use frame\actions\ActionBody;
use frame\actions\fields\StringField;

class ValidatedActionExample extends ActionBody
{
    const E_INVALID = 1;

    public function listPost(): array
    {
        return [
            // Must not begin from _ symbol to pass tests.
            'name' => StringField::class
        ];
    }

    public function validate(array $post, array $files): array
    {
        if ($post['name']->get()[0] === '_') return [self::E_INVALID];
        else return [];
    }

    public function succeed(array $post, array $files)
    {
        // No implementaion is here.
    }
}