<?php

use frame\core\Core;
use frame\events\Events;
use frame\route\Request;
use frame\views\macros\ShowPage;
use PHPUnit\Framework\TestCase;
use tests\route\stubs\RequestStub;
use frame\views\ViewRouter;
use tests\views\stubs\ViewRouterStub;

class ShowPageTest extends TestCase
{
    public function testShowsPageFromCurrentRoute()
    {
        $app = new Core;
        $app->replaceDriver(Request::class, RequestStub::class);
        $app->replaceDriver(ViewRouter::class, ViewRouterStub::class);
        RequestStub::getDriver()->setRequest('/profile/');
        Events::getDriver()->on(Core::EVENT_APP_START, new ShowPage);

        $this->expectOutputString('Jed');
        $app->exec();
    }
}