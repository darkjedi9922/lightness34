<?php namespace tests\engine;

use frame\actions\Action;

class UserDeleteAction extends Action
{
    protected function succeed(array $post, array $files)
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