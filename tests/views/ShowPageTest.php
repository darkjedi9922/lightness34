<?php

use frame\core\Core;
use frame\macros\Events;
use frame\route\Request;
use frame\views\macros\ShowPage;
use PHPUnit\Framework\TestCase;
use tests\views\stubs\RequestStub;
use frame\views\ViewRouter;
use tests\views\stubs\ViewRouterStub;

class ShowPageTest extends TestCase
{
    public function testShowsPageFromCurrentRoute()
    {
        $app = new Core;
        $app->replaceDriver(Request::class, RequestStub::class);
        $app->replaceDriver(ViewRouter::class, ViewRouterStub::class);
        RequestStub::get()->setRequest('/profile/');
        Events::get()->on(Core::EVENT_APP_START, new ShowPage);

        $this->expectOutputString('Jed');
        $app->exec();
    }
}