<?php namespace tests\examples\actions;

use frame\actions\Action;

class EmptyActionExample extends Action
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