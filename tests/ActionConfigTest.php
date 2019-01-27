<?php

use PHPUnit\Framework\TestCase;
use tests\engine\JsonValidatedAction;
use frame\actions\rules\BaseActionRules;
use frame\tools\Json;
use frame\actions\NoRuleError;
use frame\actions\Action;
use frame\actions\RuleCheckFailedException;
use tests\engine\UserDeleteAction;
use function lightlib\dump;
use frame\actions\UploadedFile;
use frame\actions\rules\ActionFileRules;

/**
 * `Run in separate process` заглушает сообщения вида `headers already sent`, когда
 * устанавливаются куки. Используется вместо (слегка костыльной) @ заглушки.
 * 
 * @runTestsInSeparateProcesses
 */
class ActionJsonValidateTest extends TestCase
{
    public function testCallbackValidate()
    {
        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);

        // Методы классов ActionRules возвращают callback-функции, проверяющие
        // переданные в них значения.
        $rules = new BaseActionRules();

        // addValidationRule устанавливает callback-функцию на ключевое слово 
        // правила, которое можно затем использовать в json-валидации.
        $action->setRule('mandatory', $rules->getMandatoryRule());

        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');
        $action->setConfig($config);

        $action->exec();

        // В экшн не было передано post значения `username`.
        $this->assertTrue($action->hasDataError('post', 'username', 'mandatory'));
        $this->assertTrue($action->isFail());
    }

    public function testRuleIsNotFoundRaisesError()
    {
        $action = new JsonValidatedAction([], '', Action::NO_RULE_ERROR);
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');
        $action->setConfig($config);

        $this->expectException(NoRuleError::class);
        
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
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');

        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setConfig($config);
        
        $baseRules = new BaseActionRules;
        $action->setRule('emptiness', $baseRules->getEmptinessRule());
        $action->setRule('min-length', $baseRules->getMinLengthRule());
        
        $action->setData('post', 'username', '');
        $action->exec();

        $emptyError = $action->hasDataError('post', 'username', 'emptiness');
        $minLengthError = $action->hasDataError('post', 'username', 'min-length');
        $this->assertTrue($emptyError && !$minLengthError);
    }

    public function testRuleHandlerMayNotStopRuleHandling()
    {
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');

        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setConfig($config);

        $baseRules = new BaseActionRules;
        $action->setRule('emptiness', $baseRules->getEmptinessRule());
        $action->setRule('min-length', $baseRules->getMinLengthRule());

        $action->setData('post', 'username', 'Jed');
        $action->exec();

        // emptiness теперь должно пройти нормально и позволить остальным проверкам
        // проверять то, что они там проверяют. 
        $emptyError = $action->hasDataError('post', 'username', 'emptiness');
        $minLengthError = $action->hasDataError('post', 'username', 'min-length');
        $this->assertTrue(!$emptyError && $minLengthError);
    }

    public function testDefaultValue()
    {
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');

        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setConfig($config);

        $this->assertEquals('Doctor Who', $action->getDataDefault('post', 'alter', false));
        $this->assertEquals('TARDIS', $action->getDataDefault('post', 'alter', true));
        $this->assertEquals('Dalek', $action->getDataDefault('post', 'enemy', false));
        $this->assertEquals('Dalek', $action->getDataDefault('post', 'enemy', true));
        $this->assertEquals(null, $action->getDataDefault('post', 'true-name', false));
        $this->assertEquals('', $action->getDataDefault('post', 'true-name', true));
    }

    public function testGetField()
    {
        $config = new Json(null);
        $config->post = [
            'answer' => [
                'default' => [42]
            ],
            'question' => [
                'default' => ['...']
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
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');

        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setConfig($config);

        $rules = new BaseActionRules;
        $action->setRule('max-length', $rules->getMaxLengthRule());

        $this->expectException(RuleCheckFailedException::class);

        $action->setData('post', 'username', 'Kostyak');
        $action->exec();
    }

    public function testInnerInterData()
    {
        $action = new UserDeleteAction([], '');
        $config = new Json(ROOT_DIR . '/tests/config/actions/UserDeleteAction.json');
        $action->setConfig($config);

        // В этом тестовом экшне id = 1 является единственным путем успешно 
        // пройти проверки.
        $action->setData('post', 'id', 1);

        // В теле экшна используются промежуточные данные. Если их нет, будет ошибка.
        $action->exec();

        $this->assertTrue($action->isSuccess());
    }

    public function testGetSetup()
    {
        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setDataAll('get', ['arg1' => 1, 'arg2' => 2]);
        $action->setData('get', 'arg3', 3);

        $this->assertEquals(1, $action->getData('get', 'arg1'));
        $this->assertEquals(2, $action->getData('get', 'arg2'));
        $this->assertEquals(3, $action->getData('get', 'arg3'));
        $this->assertEquals(null, $action->getData('get', 'arg4'));
    }

    public function testRegexpRuleFindsErrorInWrongValue()
    {
        $rules = new BaseActionRules;
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');
        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setRule('regexp', $rules->getRegexpRule());
        $action->setConfig($config);

        $action->setData('get', 'user_id', '008');
        $action->exec();

        $this->assertTrue($action->hasDataError('get', 'user_id', 'regexp'));
    }

    public function testRegexpRuleDoesNotFindErrorInCorrectValue()
    {
        $rules = new BaseActionRules;
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');
        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setRule('regexp', $rules->getRegexpRule());
        $action->setConfig($config);

        $action->setData('get', 'user_id', '007');
        $action->exec();

        $this->assertFalse($action->hasDataError('get', 'user_id', 'regexp'));
    }

    public function testReturnsDefaultGetValue()
    {
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');
        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setConfig($config);

        $this->assertEquals('some-user', $action->getDataDefault('get', 'user_id'));
    }

    public function testFileMaxSizeRuleCanFindOutError()
    {
        $rules = new ActionFileRules;
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');
        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setConfig($config);
        $action->setRule('max-size', $rules->getMaxSizeRule());

        $_FILES = ['avatar' => [
            'name' => 'my-new-avatar.jpg',
            'type' => 'image/gif',
            'size' => 1024 * 1024 * 1024, // 1 GB
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_OK
        ]];
        $action->setData($action::DATA_FILES, 'avatar', new UploadedFile('avatar'));

        $action->exec();

        $hasError = $action->hasDataError($action::DATA_FILES, 'avatar', 'max-size');
        $this->assertTrue($hasError);
    }

    public function testFileMaxSizeRuleCanFindOutSuccess()
    {
        $rules = new ActionFileRules;
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');
        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setConfig($config);
        $action->setRule('max-size', $rules->getMaxSizeRule());

        $_FILES = ['avatar' => [
            'name' => 'my-new-avatar.jpg',
            'type' => 'image/gif',
            'size' => 1024 * 1024, // 1 MB
            'tmp_name' => '',
            'error' => UploadedFile::UPLOAD_ERR_OK
        ]];
        $action->setData($action::DATA_FILES, 'avatar', new UploadedFile('avatar'));

        $action->exec();

        $hasError = $action->hasDataError($action::DATA_FILES, 'avatar', 'max-size');
        $this->assertFalse($hasError);
    }
}