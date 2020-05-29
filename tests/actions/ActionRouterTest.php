<?php

use PHPUnit\Framework\TestCase;
use frame\actions\Action;
use frame\actions\ActionRouter;
use tests\actions\examples\GetListActionExample;
use tests\engine\UserDeleteAction;
use frame\core\Core;
use frame\route\Router;
use frame\stdlib\drivers\route\UrlRouter;
use tests\actions\examples\PostListActionExample;
use tests\actions\examples\BoolPostListActionExample;

class ActionRouterTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $app = new Core([Router::class => UrlRouter::class]);
    }

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

    public function testGetsTheRightTriggerUrlWithDescribedArgs()
    {
        $action = new Action(new GetListActionExample, [
            'name' => 'Jed',
            'amount' => 66,
            'undescribed' => false
        ]);

        $url = '/tests/actions/examples/GetListActionExample'
            .'?action=&name=Jed&amount=66&undescribed=0';

        $this->assertEquals($url, (new ActionRouter)->getTriggerUrl($action));
    }

    public function testCreatesFromTriggerUrlWithDescribedArgs()
    {
        $router = new ActionRouter;
        $triggerAction = new Action(new GetListActionExample, [
            'name' => 'Jed',
            'amount' => 66,
            'undescribed' => false
        ]);
        $triggerUrl = $router->getTriggerUrl($triggerAction);

        $execAction = $router->fromTriggerUrl($triggerUrl);
        $execUrl = $router->getTriggerUrl($execAction);

        // Если Action правильно создался из триггерного запроса, то их запросы
        // должны совпасть.
        $this->assertEquals($triggerUrl, $execUrl);
    }

    /** @runInSeparateProcess */
    public function testPostFieldsAreFieldFromGlobalPost()
    {
        $router = new ActionRouter;
        $setupAction = new Action(new BoolPostListActionExample);
        $triggerUrl = $router->getTriggerUrl($setupAction);

        $_POST['sum'] = 7;
        $execAction = $router->fromTriggerUrl($triggerUrl);
        
        $this->assertEquals(7, $execAction->getData(Action::POST, 'sum'));
    }
}