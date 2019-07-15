<?php

use PHPUnit\Framework\TestCase;
use tests\engine\JsonValidatedAction;
use frame\actions\Action;
use tests\engine\UserDeleteAction;
use frame\rules\errors\NoRuleException;
use frame\rules\errors\RuleCheckFailedException;

/**
 * `Run in separate process` заглушает сообщения вида `headers already sent`, когда
 * устанавливаются куки. Используется вместо (слегка костыльной) @ заглушки.
 * 
 * @runTestsInSeparateProcesses
 */
class ActionConfigTest extends TestCase
{
    protected $jsonValidatedActionConfig;
    protected $userDeleteActionConfig;
    /** @var JsonValidatedAction $jsonValidatedAction */
    protected $jsonValidatedAction;

    protected function setUp(): void
    {
        $this->jsonValidatedActionConfig = [
            "get" => [
                "user_id" => [
                    "rules" => [
                        "base/emptiness" => true
                    ],
                    "default" => ["some-user"]
                ]
            ],
            "post" => [
                "username" => [
                    "rules" => [
                        "base/mandatory" => true,
                        "base/emptiness" => false,
                        "base/min-length" => 4,
                        "base/max-length" => 4
                    ],
                    "errorRules" => [
                        "base/max-length"
                    ]
                ],
                "alter" => [
                    "default" => ["Doctor Who", "TARDIS"],
                    "rules" => [
                        "base/mandatory" => true
                    ]
                ],
                "enemy" => [
                    "default" => ["Dalek"]
                ]
            ],
            "files" => [
                "avatar" => [
                    "default" => ["no-avatar.jpg"],
                    "rules" => [
                        "file/must-load" => false
                    ]
                ]
            ]
        ];

        $this->userDeleteActionConfig = [
            "post" => [
                "id" => [
                    "rules" => [
                        "base/mandatory" => true,
                        "base/emptiness" => false,
                        "userIdExists" => true
                    ]
                ]
            ]
        ];

        $this->jsonValidatedAction = new JsonValidatedAction;
        $this->jsonValidatedAction->setData(Action::ARGS, Action::TOKEN, 
            $this->jsonValidatedAction->getExpectedToken());
    }

    public function testCallbackValidate()
    {
        $action = new JsonValidatedAction;
        $action->setData(Action::ARGS, Action::TOKEN, $action->getExpectedToken());
        $action->setConfig($this->jsonValidatedActionConfig);

        $action->exec();

        // В экшн не было передано post значения `username`.
        $this->assertTrue($action->hasDataError('post', 'username', 'mandatory'));
        $this->assertTrue($action->isFail());
    }

    public function testIfThereAreNoErrorsThenActionIsSuccess()
    {
        // Никакие правила не устанавлены, поэтому экшн всегда будет без ошибок.
        $action = $this->jsonValidatedAction;
        $action->exec();
        $this->assertTrue($action->isSuccess());
    }

    public function testIfThereAreErrorsThenActionIsFailed()
    {
        $action = $this->jsonValidatedAction;
        $action->setConfig([
            'get' => [
                'login' => [
                    'rules' => [
                        'base/mandatory' => true
                    ]
                ]
            ]
        ]);
        $action->exec();
        $this->assertTrue($action->isFail());
    }

    public function testRuleIsNotFoundRaisesError()
    {
        $action = new JsonValidatedAction;
        $action->setData(Action::ARGS, Action::TOKEN, $action->getExpectedToken());
        $action->setConfig($this->jsonValidatedActionConfig);

        $this->expectException(NoRuleException::class);
        
        // В конфиге экшна установлены проверки, механизмы которых не были
        // установлены в экшн.
        $action->exec();
    }

    public function testRuleHandlerCanStopRuleHandling()
    {
        $action = new JsonValidatedAction;
        $action->setData(Action::ARGS, Action::TOKEN, $action->getExpectedToken());

        // По конфигу, поле username не должно быть пустым (правило emptiness).
        // Также минимальная длинна этого поля равна 4. Но бессмысленно проверять
        // минимальную длинну поля, (да и другие проверки) если оно пустое. 
        // Поэтому правило emptiness при своей обработке должно остановить 
        // проверку дальнейших правил для этого поля.
        $action->setConfig($this->jsonValidatedActionConfig);
        
        $action->setData('post', 'username', '');
        $action->exec();

        $emptyError = $action->hasDataError('post', 'username', 'emptiness');
        $minLengthError = $action->hasDataError('post', 'username', 'min-length');
        $this->assertTrue($emptyError && !$minLengthError);
    }

    public function testRuleHandlerMayNotStopRuleHandling()
    {
        $action = new JsonValidatedAction;
        $action->setData(Action::ARGS, Action::TOKEN, $action->getExpectedToken());
        $action->setConfig($this->jsonValidatedActionConfig);

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
        $config = [
            'post' => [
                'answer' => [
                    'default' => [42]
                ],
                'question' => [
                    'default' => ['...']
                ]
            ]
        ];

        $action = new UserDeleteAction;
        $action->setConfig($config);

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
        $action = new JsonValidatedAction;
        $action->setData(Action::ARGS, Action::TOKEN, $action->getExpectedToken());
        $action->setConfig($this->jsonValidatedActionConfig);

        $this->expectException(RuleCheckFailedException::class);

        $action->setData('post', 'username', 'Kostyak');
        $action->exec();
    }

    public function testInnerInterDataReturnsNotNullValue()
    {
        $action = new UserDeleteAction;
        $action->setData(Action::ARGS, Action::TOKEN, $action->getExpectedToken());
        $action->setConfig($this->userDeleteActionConfig);

        // В этом тестовом экшне id = 1 является единственным путем успешно 
        // пройти проверки.
        $action->setData('post', 'id', 1);

        // В теле экшна используются промежуточные данные. Если их нет, будет ошибка.
        $action->exec();

        $this->assertTrue($action->isSuccess());
    }

    public function testInnerInterDataReturnsNullValue()
    {
        $action = new UserDeleteAction;
        $this->assertNull($action->getInterData('post', 'some-field', 'no-value'));
    }

    public function testGetSetup()
    {
        $action = new JsonValidatedAction;
        $action->setDataAll('get', ['arg1' => 1, 'arg2' => 2]);
        $action->setData('get', 'arg3', 3);

        $this->assertEquals(1, $action->getData('get', 'arg1'));
        $this->assertEquals(2, $action->getData('get', 'arg2'));
        $this->assertEquals(3, $action->getData('get', 'arg3'));
        $this->assertEquals(null, $action->getData('get', 'arg4'));
    }

    public function testReturnsDefaultGetValue()
    {
        $action = new JsonValidatedAction;
        $action->setConfig($this->jsonValidatedActionConfig);

        $this->assertEquals('some-user', $action->getDataDefault('get', 'user_id'));
    }
    
    public function testFileDefaultValue()
    {
        $action = new JsonValidatedAction;
        $action->setConfig($this->jsonValidatedActionConfig);

        $default = $action->getDataDefault('files', 'avatar');
        $this->assertEquals('no-avatar.jpg', $default);
    }
}