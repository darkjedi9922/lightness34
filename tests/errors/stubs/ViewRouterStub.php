<?php namespace tests\errors\stubs;

use frame\views\ViewRouter;

class ViewRouterStub extends ViewRouter
{
    public function getBaseFolder(): string
    {
        return ROOT_DIR . '/tests/errors/views';
    }
}