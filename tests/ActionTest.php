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
        $get = ['object' => 1, 'subject' => 21];
        $actionId = 'del';
        $action = new UserDeleteAction($get, $actionId, Action::NO_RULE_IGNORE);

        $slash = '%255C'; // \ coded
        $and = '%3B'; // ; coded
        $equals = '%3D'; // = coded

        $url = "?action=_id${equals}${actionId}${and}".
            "_type${equals}tests${slash}engine${slash}UserDeleteAction${and}".
            "object${equals}1${and}subject${equals}21";

        $this->assertEquals($url, $action->getUrl(new Router));
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreatesFromTriggerUrl()
    {
        $triggerRouter = new Router;
        $triggerAction = new UserDeleteAction(['answer' => 42], 'del');
        $triggerUrl = $triggerAction->getUrl($triggerRouter);
        
        $execRouter = new Router($triggerUrl);
        $execAction = Action::fromTriggerUrl($execRouter->getArg('action'));
        $execUrl = $execAction->getUrl($execRouter);

        // Если Action правильно создался из триггерного запроса, то их запросы
        // должны совпасть.
        $this->assertEquals($triggerUrl, $execUrl);
    }
}