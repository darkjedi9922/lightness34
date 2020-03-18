<?php

use frame\core\Core;
use frame\views\View;
use frame\views\ViewRouter;
use PHPUnit\Framework\TestCase;
use tests\views\stubs\DerivedViewStub;
use tests\views\stubs\ViewRouterStub;

class ViewTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $app = new Core;
        $app->replaceDriver(ViewRouter::class, ViewRouterStub::class);
    }

    public function testEmptyNamespaceIsRoot()
    {
        $view = new View('empty');
        $this->assertTrue($view->isInNamespace(''));
    }

    public function testViewCanBeInNonEmptyNamespace()
    {
        $view = new DerivedViewStub('empty');
        $this->assertTrue($view->isInNamespace('derived'));
    }

    public function testNamespacesAreFilesystemTree()
    {
        $view = new View('tree/twiced/leaf');
        $this->assertTrue($view->isInNamespace('tree'));
    }
}