<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\route\Router;
use frame\http\route\UrlRouter;
use frame\views\Alias;
use frame\views\ViewRouter;
use tests\views\stubs\ViewRouterStub;

class AliasTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $app = new Core([
            Router::class => UrlRouter::class,
            ViewRouter::class => ViewRouterStub::class
        ]);
    }

    public function testResolvesAnAliasByARoute()
    {
        $expectedAlias = 'some/alias/string';
        $actualAlias = Alias::resolveAlias('some-alias');
        $this->assertEquals($expectedAlias, (string) $actualAlias);
    }

    public function testReturnsNullIfAnAliasIsNotFound()
    {
        $this->assertNull(Alias::resolveAlias('some-non-existence-alias'));
    }

    public function testSupportsDynamicRouting()
    {
        $expectedAlias = 'some/dynamic/alias';
        $expectedDynamicArgs = ['dynamic-parameter'];
        
        $actualAlias = Alias::resolveAlias("dynamic-parameter/dynamic-alias");
        
        $this->assertNotNull($actualAlias);
        $this->assertEquals($expectedAlias, $actualAlias->getContents());
        $this->assertEquals($expectedDynamicArgs, $actualAlias->getDynamicArgs());
    }
}