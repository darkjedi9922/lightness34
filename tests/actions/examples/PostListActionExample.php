<?php namespace tests\actions\examples;

use frame\actions\ActionBody;

class PostListActionExample extends ActionBody
{
    public function listPost(): array
    {
        return [
            'sum' => [self::POST_INT, 'Some integer arg']
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