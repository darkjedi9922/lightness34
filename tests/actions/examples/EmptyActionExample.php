<?php namespace tests\actions\examples;

use frame\actions\ActionBody;

class EmptyActionExample extends ActionBody
{
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