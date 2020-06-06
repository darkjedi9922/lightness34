<?php
use PHPUnit\Framework\TestCase;
use frame\lists\paged\PagerModel;
use frame\core\Core;
use frame\route\Router;
use frame\http\route\UrlRouter;
use frame\route\Request;
use tests\route\stubs\RequestStub;

class PagerModelTest extends TestCase
{
    /** @runInSeparateProcess */
    public function testGetsCurrentPageFromCurrentRouteParameterP()
    {
        $app = new Core([
            Router::class => UrlRouter::class,
            Request::class => RequestStub::class
        ]);

        RequestStub::getDriver()->setRequest('?p=3');

        $this->assertEquals(3, PagerModel::getRoutePage());
    }
}