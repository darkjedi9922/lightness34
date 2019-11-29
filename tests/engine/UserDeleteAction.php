<?php namespace tests\engine;

use frame\actions\Action;

class UserDeleteAction extends Action
{
    protected function succeed(array $post, array $files)
    {
        
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