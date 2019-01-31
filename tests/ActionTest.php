<?php

use PHPUnit\Framework\TestCase;
use tests\engine\UserDeleteAction;
use frame\actions\Action;
use frame\route\Router;

use function lightlib\http_parse_query;

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
        $wtf = '0%3D';

        $url = "?action=${wtf}del_tests${slash}engine${slash}UserDeleteAction".
            "${and}object${equals}1${and}subject${equals}21";

        $this->assertEquals($url, $action->getUrl(new Router));
    }
}