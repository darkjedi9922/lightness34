<?php namespace tests\engine;

use frame\actions\ActionBody;

class JsonValidatedAction extends ActionBody
{
    public function succeed(array $post, array $files)
    {
        // Some actions...

        // $avatar = $this->getData(self::FILES, 'avatar'); // File
        // echo 'Handling a file with a size:' . $avatar->getSize();

        // Some actions ...
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