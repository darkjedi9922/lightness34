<?php

use frame\console\route\ConsoleRouter;
use PHPUnit\Framework\TestCase;

class ConsoleRouterTest extends TestCase
{
    public function testParsesARoute()
    {
        $router = new ConsoleRouter;
        $route = $router->parseRoute('config rights');

        $this->assertEquals('config rights', $route->url);
        $this->assertEquals('config/rights', $route->pagename);
        $this->assertEquals([], $route->args);
    }
}