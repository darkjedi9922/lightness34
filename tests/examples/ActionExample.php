<?php namespace tests\examples;

use frame\actions\Action;

class ActionExample extends Action
{
    public function listGet(): array
    {
        return [
            'name' => [self::GET_STRING, 'This is a description of the get arg'],
            'amount' => [self::GET_INT, 'Some integer arg']
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