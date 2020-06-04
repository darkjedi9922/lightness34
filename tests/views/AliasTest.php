<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\views\Alias;
use frame\views\ViewRouter;
use tests\views\stubs\ViewRouterStub;

class AliasTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $app = new Core([
            ViewRouter::class => ViewRouterStub::class
        ]);
    }

    public function testResolvesAnAliasByARoute()
    {
        $expectedAlias = 'some/alias/string';
        $actualAlias = Alias::resolveAlias('some-alias');
        $this->assertEquals($expectedAlias, $actualAlias);
    }

    public function testReturnsNullIfAnAliasIsNotFound()
    {
        $this->assertNull(Alias::resolveAlias('some-non-existence-alias'));
    }
}