<?php namespace tests\engine;

use frame\actions\ActionBody;

class UserDeleteAction extends ActionBody
{
    public function succeed(array $post, array $files)
    {
        
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