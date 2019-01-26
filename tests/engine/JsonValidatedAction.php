<?php namespace tests\engine;

use frame\actions\Action;

class JsonValidatedAction extends Action
{
    protected function successBody()
    {
        // Nothing to do
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