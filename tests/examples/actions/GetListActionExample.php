<?php namespace tests\examples\actions;

use frame\actions\ActionBody;

class GetListActionExample extends ActionBody
{
    public function listGet(): array
    {
        return [
            'name' => [self::GET_STRING, 'This is a description of the get arg'],
            'amount' => [self::GET_INT, 'Some integer arg']
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