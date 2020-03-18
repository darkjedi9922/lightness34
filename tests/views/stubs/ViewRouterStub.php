<?php namespace tests\views\stubs;

class ViewRouterStub extends \frame\views\ViewRouter
{
    public function getBaseFolder(): string
    {
        return ROOT_DIR . '/tests/views/views';
    }
}