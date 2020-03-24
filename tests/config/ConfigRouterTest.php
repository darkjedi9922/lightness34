<?php

use tests\config\stubs\ConfigRouterStub;
use frame\stdlib\configs\JsonConfig;
use frame\stdlib\configs\PhpConfig;
use PHPUnit\Framework\TestCase;

class ConfigRouterTest extends TestCase
{
    public function testFindsConfig()
    {
        $router = new ConfigRouterStub;
        $router->addSupport(JsonConfig::class);
        $router->addSupport(PhpConfig::class);

        $jsonConfig = $router->findConfig('jsonconfig');
        $phpConfig = $router->findConfig('phpconfig');

        $this->assertInstanceOf(JsonConfig::class, $jsonConfig);
        $this->assertInstanceOf(PhpConfig::class, $phpConfig);
    }

    public function testReturnsNullWhenDoesNotFindConfig()
    {
        $router = new ConfigRouterStub;
        $router->addSupport(JsonConfig::class);
        $router->addSupport(PhpConfig::class);

        $this->assertNull($router->findConfig('noconfig'));
    }
}