<?php namespace tests\examples\actions;

use frame\actions\Action;

class EmptyActionExample extends Action
{
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