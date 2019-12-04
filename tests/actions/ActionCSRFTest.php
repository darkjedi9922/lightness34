<?php

use PHPUnit\Framework\TestCase;
use tests\engine\UserDeleteAction;
use frame\actions\Action;
use frame\errors\HttpError;
use frame\actions\ActionRouter;

class ActionCSRFTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testExpectedTokenIsGottenInTheTriggerUrl()
    {
        $triggerAction = new Action(new UserDeleteAction);
        $triggerToken = $triggerAction->getExpectedToken();
        $url = (new ActionRouter)->getTriggerUrl($triggerAction);

        $router = new ActionRouter;
        $execAction = $router->fromTriggerUrl($url);
        $execToken = $execAction->getData(Action::ARGS, Action::TOKEN);

        $this->assertEquals($triggerToken, $execToken);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIncorrectTokenThrowsHttpError()
    {
        $this->expectException(HttpError::class);
        $action = new Action(new UserDeleteAction, ([Action::TOKEN => 'incorrect-token']));
        $action->exec();
    }

    /**
     * @runInSeparateProcess
     */
    public function testNoTokenIsIncorrectToken()
    {
        $this->expectException(HttpError::class);
        // Никакой токен не передается.
        $action = new Action(new UserDeleteAction);
        $action->exec();
    }
}