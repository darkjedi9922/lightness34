<?php

use frame\core\Core;
use frame\route\Request;
use PHPUnit\Framework\TestCase;
use tests\route\examples\RouteNamespaceMacroExample;
use tests\route\stubs\RequestStub;

/**
 * @runTestsInSeparateProcesses
 */
class RouteNamespaceMacroTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $app = new Core;
        $app->replaceDriver(Request::class, RequestStub::class);
    }

    public function testRunsWhenTheRouteNamespaceMatchesCurrentRoute()
    {
        RequestStub::getDriver()->setRequest('/api/users/list');
        $macro = new RouteNamespaceMacroExample('api');
        
        $this->assertFalse($macro->wasRun);
        $macro->exec();
        $this->assertTrue($macro->wasRun);
    }

    public function testDoesNotRunWhenTheRouteNamespaceDoesNotMatchCurrentRoute()
    {
        RequestStub::getDriver()->setRequest('/users/profile/1');
        $macro = new RouteNamespaceMacroExample('api');

        $this->assertFalse($macro->wasRun);
        $macro->exec();
        $this->assertFalse($macro->wasRun);
    }
}