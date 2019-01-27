<?php namespace tests\engine;

use frame\actions\Action;

class JsonValidatedAction extends Action
{
    protected function successBody()
    {
        // Some actions...

        // $avatar = $this->getData(self::DATA_FILES, 'avatar'); // File
        // echo 'Handling a file with a size:' . $avatar->getSize();

        // Some actions ...
    }

    protected function getSuccessRedirect()
    {
        return null;
    }

    protected function getFailRedirect()
    {
        return null;
    }
}