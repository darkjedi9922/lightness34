<?php namespace tests\examples\actions;

use frame\actions\Action;

class GetListActionExample extends Action
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

    public function getSuccessRedirect(): ?string
    {
        return null;
    }

    public function getFailRedirect(): ?string
    {
        return null;
    }
}