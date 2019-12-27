<?php
use PHPUnit\Framework\TestCase;
use tests\views\stubs\ViewStub;
use tests\views\stubs\DerivedViewStub;

class ViewTest extends TestCase
{
    public function testEmptyNamespaceIsRoot()
    {
        $view = new ViewStub('empty');
        $this->assertTrue($view->isInNamespace(''));
    }

    public function testViewCanBeInNonEmptyNamespace()
    {
        $view = new DerivedViewStub('empty');
        $this->assertTrue($view->isInNamespace('derived'));
    }

    public function testNamespacesAreFilesystemTree()
    {
        $view = new ViewStub('tree/twiced/leaf');
        $this->assertTrue($view->isInNamespace('tree'));
    }
}