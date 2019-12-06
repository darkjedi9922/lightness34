<?php

use PHPUnit\Framework\TestCase;
use tests\engine\UserDeleteAction;
use tests\examples\actions\GetListActionExample;
use frame\errors\HttpError;
use tests\examples\actions\PostListActionExample;
use frame\actions\ActionRouter;
use frame\actions\Action;

class ActionTest extends TestCase
{
    public function testDefaultIdIsEmptyString()
    {
        $action = new Action(new UserDeleteAction);
        $this->assertEquals('', $action->getId());
    }

    public function testIdIsEmptyStringIfItWasNotRecievedFromUrl()
    {
        $router = new ActionRouter;
        $action = $router->fromTriggerUrl('/tests/engine/UserDeleteAction');
        $this->assertEquals('', $action->getId());
    }

    public function testThrowsNotFoundIfThereIsNotRecievedListedGetData()
    {
        $action = new Action(new GetListActionExample);

        $this->expectException(HttpError::class);
        $this->expectExceptionCode(HttpError::NOT_FOUND);
        $action->exec();
    }

    public function testDoesNotThrowNotFoundIfThereIsRecievedListedGetData()
    {
        $action = new Action(new GetListActionExample, [
            'name' => 'SomeName',
            'amount' => '12'
        ]);
        $action->exec();
        $this->assertFalse($action->hasErrors());
    }

    public function testConvertsGetArgsToSpecifiedType()
    {
        $action = new Action(new GetListActionExample, ['amount' => '42']);
        $amount = $action->getData('get', 'amount');

        $this->assertIsInt($amount);
    }

    public function testThrowsNotFoundIfThereIsNotRecievedListedPostData()
    {
        $action = new Action(new PostListActionExample);
        $this->expectException(HttpError::class);
        $this->expectExceptionCode(HttpError::NOT_FOUND);
        $action->exec();
    }

    public function testDoesNotThrowNotFoundIfThereIsRecievedListedPostData()
    {
        $action = new Action(new PostListActionExample);
        $action->setData('post', 'sum', '66');
        $action->exec();
        $this->assertFalse($action->hasErrors());
    }

    public function testConvertsPostArgsToSpecifiedType()
    {
        $action = new Action(new PostListActionExample);
        $action->setData('post', 'sum', '66');
        
        $value = $action->getData('post', 'sum');
        $this->assertIsInt($value);
    }
}