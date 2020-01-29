<?php namespace tests\actions\examples;

use frame\actions\ActionBody;

class GetListActionExample extends ActionBody
{
    public function listGet(): array
    {
        return [
            'name' => self::GET_STRING,
            'amount' => self::GET_INT
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