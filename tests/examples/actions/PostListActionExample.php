<?php namespace tests\examples\actions;

use frame\actions\Action;

class PostListActionExample extends Action
{
    public function listPost(): array
    {
        return [
            'sum' => [self::POST_INT, 'Some integer arg']
        ];
    }

    protected function succeed(array $post, array $files)
    {
        // Here is nothing to do.
    }

    protected function getSuccessRedirect(): ?string
    {
        return null;
    }

    protected function getFailRedirect(): ?string
    {
        return null;
    }
}