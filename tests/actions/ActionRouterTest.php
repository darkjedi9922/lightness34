<?php

use PHPUnit\Framework\TestCase;
use frame\actions\ActionRouter;
use tests\engine\UserDeleteAction;

class ActionRouterTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testCreatesFromTriggerUrl()
    {
        $triggerAction = new UserDeleteAction(['answer' => 42], 'del');
        $triggerUrl = $triggerAction->getUrl();

        $router = new ActionRouter;
        $execAction = $router->fromTriggerUrl($triggerUrl);
        $execUrl = $execAction->getUrl();

        // Если Action правильно создался из триггерного запроса, то их запросы
        // должны совпасть.
        $this->assertEquals($triggerUrl, $execUrl);
    }
}