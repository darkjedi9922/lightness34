<?php

use PHPUnit\Framework\TestCase;
use tests\engine\UserDeleteAction;
use frame\actions\Action;
use frame\errors\HttpError;

class ActionCSRFTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testExpectedTokenIsGottenInTheTriggerUrl()
    {
        $triggerAction = new UserDeleteAction;
        $triggerToken = $triggerAction->getExpectedToken();
        $url = $triggerAction->getUrl();

        $execAction = UserDeleteAction::fromTriggerUrl($url);
        $execToken = $execAction->getData(Action::ARGS, Action::TOKEN);

        $this->assertEquals($triggerToken, $execToken);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIncorrectTokenThrowsHttpError()
    {
        $this->expectException(HttpError::class);
        $action = new UserDeleteAction([Action::TOKEN => 'incorrect-token']);
        $action->exec();
    }

    /**
     * @runInSeparateProcess
     */
    public function testNoTokenIsIncorrectToken()
    {
        $this->expectException(HttpError::class);
        // Никакой токен не передается.
        $action = new UserDeleteAction;
        $action->exec();
    }
}