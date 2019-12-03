<?php namespace tests\examples;

use frame\actions\Action;

class ActionExample extends Action
{
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