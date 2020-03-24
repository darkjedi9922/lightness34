<?php namespace tests\config\stubs;

use frame\config\ConfigRouter;

class ConfigRouterStub extends ConfigRouter
{
    public function getConfigsDir(): string
    {
        return ROOT_DIR . '/tests/config/examples';
    }
}