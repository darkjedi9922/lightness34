<?php

use PHPUnit\Framework\TestCase;
use tests\engine\UserDeleteAction;
use frame\actions\Action;
use tests\examples\ActionExample;

/**
 * @runTestsInSeparateProcesses
 */
class ActionTest extends TestCase
{
    public function testUrl()
    {
        $get = [Action::ID => 'del', 'object' => 1, 'subject' => 21];
        $action = new UserDeleteAction($get);

        $url = "/tests/engine/UserDeleteAction?action=del&_csrf={$action->getExpectedToken()}&object=1&subject=21";

        $this->assertEquals($url, $action->getUrl());
    }

    public function testUrlWithoutId()
    {
        $get = ['object' => 1, 'subject' => 21];
        $action = new UserDeleteAction($get);

        $url = "/tests/engine/UserDeleteAction?action=&_csrf={$action->getExpectedToken()}&object=1&subject=21";

        $this->assertEquals($url, $action->getUrl());
    }

    public function testDefaultIdIsEmptyString()
    {
        $action = new UserDeleteAction;
        $this->assertEquals('', $action->getId());
    }

    public function testIdIsEmptyStringIfItWasNotRecievedFromUrl()
    {
        $action = Action::fromTriggerUrl('/tests/engine/UserDeleteAction');
        $this->assertEquals('', $action->getId());
    }

    public function testCreatesFromTriggerUrl()
    {
        $triggerAction = new UserDeleteAction(['answer' => 42], 'del');
        $triggerUrl = $triggerAction->getUrl();
        
        $execAction = Action::fromTriggerUrl($triggerUrl);
        $execUrl = $execAction->getUrl();

        // Если Action правильно создался из триггерного запроса, то их запросы
        // должны совпасть.
        $this->assertEquals($triggerUrl, $execUrl);
    }

    public function testSetsTokenToTheGetAndReturnsIt()
    {
        $action = new ActionExample;
        $this->assertNull($action->getData('get', '_csrf'));

        $token = $action->getExpectedToken();
        $action->setToken($token);

        $this->assertEquals($token, $action->getData('get', '_csrf'));
        $this->assertEquals($token, $action->getToken());
    }
}