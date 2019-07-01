<?php

use PHPUnit\Framework\TestCase;
use tests\engine\UserDeleteAction;
use frame\actions\Action;
use frame\route\Router;

class ActionTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testUrl()
    {
        $get = [Action::ID => 'del', 'object' => 1, 'subject' => 21];
        $action = new UserDeleteAction($get, Action::NO_RULE_IGNORE);

        $slash = '%255C'; // \ coded
        $and = '%3B'; // ; coded
        $equals = '%3D'; // = coded

        $url = "?action=_id${equals}del${and}".
            "object${equals}1${and}subject${equals}21${and}".
            "_type${equals}tests${slash}engine${slash}UserDeleteAction${and}".
            "_csrf${equals}{$action->getExpectedToken()}";

        $this->assertEquals($url, $action->getUrl());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreatesFromTriggerUrl()
    {
        $triggerAction = new UserDeleteAction(['answer' => 42], 'del');
        $triggerUrl = $triggerAction->getUrl();
        
        $execRouter = new Router($triggerUrl);
        $execAction = Action::fromTriggerUrl($execRouter->getArg('action'));
        $execUrl = $execAction->getUrl();

        // Если Action правильно создался из триггерного запроса, то их запросы
        // должны совпасть.
        $this->assertEquals($triggerUrl, $execUrl);
    }
}