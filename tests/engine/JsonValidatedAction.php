<?php namespace tests\engine;

use frame\actions\Action;

class JsonValidatedAction extends Action
{
    protected function succeed(array $post, array $files)
    {
        // Some actions...

        // $avatar = $this->getData(self::FILES, 'avatar'); // File
        // echo 'Handling a file with a size:' . $avatar->getSize();

        // Some actions ...
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