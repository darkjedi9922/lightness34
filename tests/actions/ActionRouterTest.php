<?php

use PHPUnit\Framework\TestCase;
use frame\actions\Action;
use frame\actions\ActionRouter;
use tests\engine\UserDeleteAction;

class ActionRouterTest extends TestCase
{
    public function testGetsATriggerUrl()
    {
        $get = [Action::ID => 'del', 'object' => 1, 'subject' => 21];
        $action = new Action(new UserDeleteAction, $get);

        $url = "/tests/engine/UserDeleteAction?action=del&object=1&subject=21";

        $this->assertEquals($url, (new ActionRouter)->getTriggerUrl($action));
    }

    public function testGetsATriggerUrlWithoutId()
    {
        $get = ['object' => 1, 'subject' => 21];
        $action = new Action(new UserDeleteAction, $get);

        $url = "/tests/engine/UserDeleteAction?action=&object=1&subject=21";

        $this->assertEquals($url, (new ActionRouter)->getTriggerUrl($action));
    }

    public function testCreatesFromTriggerUrl()
    {
        $router = new ActionRouter;
        $triggerAction = new Action(new UserDeleteAction, ['answer' => 42], 'del');
        $triggerUrl = $router->getTriggerUrl($triggerAction);

        $execAction = $router->fromTriggerUrl($triggerUrl);
        $execUrl = $router->getTriggerUrl($execAction);

        // Если Action правильно создался из триггерного запроса, то их запросы
        // должны совпасть.
        $this->assertEquals($triggerUrl, $execUrl);
    }
}