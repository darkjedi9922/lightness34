<?php

use PHPUnit\Framework\TestCase;
use tests\engine\UserDeleteAction;
use tests\actions\examples\GetListActionExample;
use tests\actions\examples\PostListActionExample;
use tests\actions\examples\BoolPostListActionExample;
use tests\actions\examples\AlwaysSucceedActionExample;
use tests\actions\examples\AlwaysFailActionExample;
use frame\errors\HttpError;
use frame\actions\ActionRouter;
use frame\actions\Action;
use frame\core\Core;
use frame\route\Router;

class ActionTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $app = new Core(new Router);
    }

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

    public function testConvertsSpecifiedIntegerPostArgsToInteger()
    {
        $action = new Action(new PostListActionExample);
        $action->setData('post', 'sum', '66');
        
        $value = $action->getData('post', 'sum');
        $this->assertIsInt($value);
    }

    /**
     * @dataProvider boolPostProvider
     */
    public function testConvertsSpecifiedPostArgsToBool($value, bool $boolValue)
    {
        $action = new Action(new BoolPostListActionExample);
        $action->setData('post', 'checked', $value);

        $convertedValue = $action->getData('post', 'checked');
        $this->assertEquals($boolValue, $convertedValue);
    }

    public function testIfSpecifiedBoolValueWasNotRecievedThenItEqualsFalse()
    {
        $action = new Action(new BoolPostListActionExample);
        $value = $action->getData('post', 'checked');
        $this->assertIsBool($value);
    }

    public function boolPostProvider() {
        return [
            [true, true],
            [false, false],
            [null, false],
            ['0', false],
            ['1', true],
            ['', false],
            ['some-text', true]
        ];
    }

    public function testNoExecutedActionHasEmptyResult()
    {
        $action = new Action(new AlwaysSucceedActionExample);
        $this->assertEmpty($action->getResult());
    }

    public function testResultOfTheSuccessIsSavedInTheActionResult()
    {
        $action = new Action(new AlwaysSucceedActionExample);
        $action->exec();
        $this->assertEquals(['resultAnswer' => 42], $action->getResult());
    }

    public function testResultOfFailIsSavedInTheActionResult()
    {
        $action = new Action(new AlwaysFailActionExample);
        $action->exec();
        $this->assertEquals(['doctor' => 'exterminate!'], $action->getResult());
    }
}