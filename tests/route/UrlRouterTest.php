<?php

use PHPUnit\Framework\TestCase;
use frame\stdlib\drivers\route\UrlRouter;

class UrlRouterTest extends TestCase
{
    /**
     * @dataProvider routeProvider
     */
    public function testParsesPagename(string $route, string $pagename)
    {
        $route = UrlRouter::getDriver()->parseRoute($route);
        $this->assertEquals($pagename, $route->pagename);
    }

    public function routeProvider(): array
    {
        return [
            ['a/b/c', 'a/b/c'],
            ['a/b/$/d', 'a/b/$/d'],
            ['/a/0/', 'a/0']
        ];
    }

    public function testChecksOnePageNamespace()
    {
        $router = UrlRouter::getDriver()->parseRoute('/articles/item?id=3');
        $this->assertTrue($router->isInNamespace('articles'));
        $this->assertTrue($router->isInNamespace('articles/item'));
        $this->assertFalse($router->isInNamespace('articles/item/id'));
    }

    public function testCheckManyPageNamespaces()
    {
        $router = UrlRouter::getDriver()->parseRoute('/articles/item?id=3');
        $this->assertTrue($router->isInAnyNamespace([
            'users',
            'images',
            'articles'
        ]));
    }

    public function testEmptyNamespaceIncludesAnyPage()
    {
        $router = UrlRouter::getDriver()->parseRoute('/articles/item?id=3');
        $this->assertTrue($router->isInNamespace(''));

        $router = UrlRouter::getDriver()->parseRoute('');
        $this->assertTrue($router->isInNamespace(''));

        $router = UrlRouter::getDriver()->parseRoute('?id=3');
        $this->assertTrue($router->isInNamespace(''));
    }

    public function testStartSlashIsIgnoredInNamespaceChecking()
    {
        $router = UrlRouter::getDriver()->parseRoute('articles/item?id=3');
        $this->assertTrue($router->isInNamespace('/articles'));
    }
}