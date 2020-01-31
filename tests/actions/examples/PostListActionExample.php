<?php namespace tests\actions\examples;

use frame\actions\ActionBody;
use frame\actions\fields\IntegerField;

class PostListActionExample extends ActionBody
{
    public function listPost(): array
    {
        return [
            'sum' => IntegerField::class
        ];
    }

    public function succeed(array $post, array $files)
    {
        // Here is nothing to do.
    }

    public function getSuccessRedirect(): ?string
    {
        return null;
    }

    public function getFailRedirect(): ?string
    {
        return null;
    }
}