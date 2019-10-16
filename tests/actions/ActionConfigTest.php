<?php

use PHPUnit\Framework\TestCase;
use tests\engine\JsonValidatedAction;
use frame\actions\Action;
use tests\engine\UserDeleteAction;
use frame\rules\errors\NoRuleException;
use frame\rules\errors\RuleCheckFailedException;
use tests\engine\JsonValidatedConfiguredAction;

/**
 * `Run in separate process` заглушает сообщения вида `headers already sent`, когда
 * устанавливаются куки. Используется вместо (слегка костыльной) @ заглушки.
 * 
 * @runTestsInSeparateProcesses
 */
class ActionConfigTest extends TestCase
{
    /** @var JsonValidatedAction $jsonValidatedAction */
    protected $jsonValidatedAction;
    /** @var JsonValidatedConfiguredAction $jsonValidatedConfiguredAction */
    protected $jsonValidatedConfiguredAction;
    /** @var UserDeleteAction $userDeleteAction */
    protected $userDeleteAction;

    protected function setUp(): void
    {
        $this->jsonValidatedAction = new JsonValidatedAction;
        $this->jsonValidatedAction->setData(Action::ARGS, Action::TOKEN, 
            $this->jsonValidatedAction->getExpectedToken());
        
        $this->jsonValidatedConfiguredAction = new JsonValidatedConfiguredAction;
        $this->jsonValidatedConfiguredAction->setData(Action::ARGS, Action::TOKEN,
            $this->jsonValidatedConfiguredAction->getExpectedToken());

        $this->userDeleteAction = new UserDeleteAction;
        $this->userDeleteAction->setData(Action::ARGS, Action::TOKEN,
            $this->userDeleteAction->getExpectedToken());
    }

    public function testCallbackValidate()
    {
        $action = $this->jsonValidatedConfiguredAction;
        $action->exec();

        // В экшн не было передано post значения `username`.
        $this->assertTrue($action->hasDataError('post', 'username', 'mandatory'));
        $this->assertTrue($action->hasErrors());
    }

    public function testIfThereAreNoErrorsThenActionIsSuccess()
    {
        // Никакие правила не устанавлены, поэтому экшн всегда будет без ошибок.
        $action = $this->jsonValidatedAction;
        $action->exec();
        $this->assertTrue(!$action->hasErrors());
    }

    public function testIfThereAreErrorsThenActionIsFailed()
    {
        // Как минимум, не передали обязательные поля.
        $action = $this->jsonValidatedConfiguredAction;
        $action->exec();
        $this->assertTrue($action->hasErrors());
    }

    public function testRuleIsNotFoundRaisesError()
    {
        $action = $this->jsonValidatedConfiguredAction;
        $this->expectException(NoRuleException::class);
        // В конфиге экшна установлены проверки, механизмы которых не были
        // установлены в экшн.
        $action->exec();
    }

    public function testRuleHandlerCanStopRuleHandling()
    {
        // По конфигу, поле username не должно быть пустым (правило emptiness).
        // Также минимальная длинна этого поля равна 4. Но бессмысленно проверять
        // минимальную длинну поля, (да и другие проверки) если оно пустое. 
        // Поэтому правило emptiness при своей обработке должно остановить 
        // проверку дальнейших правил для этого поля.
        $action = $this->jsonValidatedConfiguredAction;
        
        $action->setData('post', 'username', '');
        $action->exec();

        $emptyError = $action->hasDataError('post', 'username', 'emptiness');
        $minLengthError = $action->hasDataError('post', 'username', 'min-length');
        $this->assertTrue($emptyError && !$minLengthError);
    }

    public function testRuleHandlerMayNotStopRuleHandling()
    {
        $action = $this->jsonValidatedConfiguredAction;
        $action->setData('post', 'username', 'Jed');
        $action->exec();

        // emptiness теперь должно пройти нормально и позволить остальным проверкам
        // проверять то, что они там проверяют. 
        $emptyError = $action->hasDataError('post', 'username', 'emptiness');
        $minLengthError = $action->hasDataError('post', 'username', 'min-length');
        $this->assertTrue(!$emptyError && $minLengthError);
    }

    public function testGetField()
    {
        $action = $this->userDeleteAction;
        $action->setData('post', 'username', 'BadUser');
        $action->setData('post', 'empty-field', '');
        $action->setData('post', 'question', '');
        
        $this->assertEquals('BadUser', $action->getData('post', 'username'));
        $this->assertEquals('', $action->getData('post', 'empty-field'));
        $this->assertEquals(null, $action->getData('post', 'non-existence-field'));
        $this->assertEquals(42, $action->getData('post', 'answer'));
        $this->assertEquals('...', $action->getData('post', 'question'));
    }

    public function testFailedRuleMayThrowException()
    {
        $action = $this->jsonValidatedConfiguredAction;

        $this->expectException(RuleCheckFailedException::class);

        $action->setData('post', 'username', 'Kostyak');
        $action->exec();
    }

    public function testInnerInterDataReturnsNotNullValue()
    {
        $action = $this->userDeleteAction;

        // В этом тестовом экшне id = 1 является единственным путем успешно 
        // пройти проверки.
        $action->setData('post', 'id', 1);

        // В теле экшна используются промежуточные данные. Если их нет, будет ошибка.
        $action->exec();

        $this->assertTrue(!$action->hasErrors());
    }

    public function testInnerInterDataReturnsNullValue()
    {
        $action = $this->userDeleteAction;
        $this->assertNull($action->getInterData('post', 'some-field', 'no-value'));
    }

    public function testGetSetup()
    {
        $action = $this->jsonValidatedAction;
        $action->setDataAll('get', ['arg1' => 1, 'arg2' => 2]);
        $action->setData('get', 'arg3', 3);

        $this->assertEquals(1, $action->getData('get', 'arg1'));
        $this->assertEquals(2, $action->getData('get', 'arg2'));
        $this->assertEquals(3, $action->getData('get', 'arg3'));
        $this->assertEquals(null, $action->getData('get', 'arg4'));
    }

    public function testReturnsDefaultGetValue()
    {
        $action = $this->jsonValidatedConfiguredAction;
        $this->assertEquals('some-user', $action->getDataDefault('get', 'user_id'));
    }
    
    public function testFileDefaultValue()
    {
        $action = $this->jsonValidatedConfiguredAction;
        $default = $action->getDataDefault('files', 'avatar');
        $this->assertEquals('no-avatar.jpg', $default);
    }
}