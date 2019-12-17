<?php

use PHPUnit\Framework\TestCase;
use frame\route\Router;

class RouterTest extends TestCase
{
    public function testChecksOnePageNamespace()
    {
        $router = new Router('/articles/item?id=3');
        $this->assertTrue($router->isInNamespace('articles'));
        $this->assertTrue($router->isInNamespace('articles/item'));
        $this->assertFalse($router->isInNamespace('articles/item/id'));
    }

    public function testCheckManyPageNamespaces()
    {
        $router = new Router('/articles/item?id=3');
        $this->assertTrue($router->isInAnyNamespace([
            'users',
            'images',
            'articles'
        ]));
    }

    public function testEmptyNamespaceIncludesAnyPage()
    {
        $router = new Router('/articles/item?id=3');
        $this->assertTrue($router->isInNamespace(''));

        $router = new Router('');
        $this->assertTrue($router->isInNamespace(''));

        $router = new Router('?id=3');
        $this->assertTrue($router->isInNamespace(''));
    }

    public function testStartSlashIsIgnoredInNamespaceChecking()
    {
        $router = new Router('articles/item?id=3');
        $this->assertTrue($router->isInNamespace('/articles'));
    }
}