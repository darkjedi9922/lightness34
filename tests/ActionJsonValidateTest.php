<?php

use PHPUnit\Framework\TestCase;
use tests\engine\JsonValidatedAction;
use frame\actions\rules\BaseActionRules;
use frame\tools\Json;
use frame\actions\NoRuleError;
use frame\actions\Action;
use frame\actions\RuleCheckFailedException;
use tests\engine\UserDeleteAction;

/**
 * `Run in separate process` заглушает сообщения вида `headers already sent`, когда
 * устанавливаются куки. Используется вместо (слегка костыльной) @ заглушки.
 */
class ActionJsonValidateTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
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
        $action->setValidationConfig($config);

        $action->exec();

        // В экшн не было передано post значения `username`.
        $this->assertTrue($action->hasPostError('username', 'mandatory'));
        $this->assertTrue($action->isFail());
    }

    /**
     * @runInSeparateProcess
     */
    public function testRuleIsNotFoundRaisesError()
    {
        $action = new JsonValidatedAction([], '', Action::NO_RULE_ERROR);
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');
        $action->setValidationConfig($config);

        $this->expectException(NoRuleError::class);
        
        // В конфиге экшна установлены проверки, механизмы которых не были
        // установлены в экшн.
        $action->exec();
    }

    /**
     * @runInSeparateProcess
     */
    public function testRuleHandlerCanStopRuleHandling()
    {
        // По конфигу, поле username не должно быть пустым (правило emptiness).
        // Также минимальная длинна этого поля равна 4. Но бессмысленно проверять
        // минимальную длинну поля, (да и другие проверки) если оно пустое. 
        // Поэтому правило emptiness при своей обработке должно остановить 
        // проверку дальнейших правил для этого поля.
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');

        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setValidationConfig($config);
        
        $baseRules = new BaseActionRules;
        $action->setRule('emptiness', $baseRules->getEmptinessRule());
        $action->setRule('min-length', $baseRules->getMinLengthRule());
        
        $action->setPostOne('username', '');
        $action->exec();

        $emptyError = $action->hasPostError('username', 'emptiness');
        $minLengthError = $action->hasPostError('username', 'min-length');
        $this->assertTrue($emptyError && !$minLengthError);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRuleHandlerMayNotStopRuleHandling()
    {
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');

        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setValidationConfig($config);

        $baseRules = new BaseActionRules;
        $action->setRule('emptiness', $baseRules->getEmptinessRule());
        $action->setRule('min-length', $baseRules->getMinLengthRule());

        $action->setPostOne('username', 'Jed');
        $action->exec();

        // emptiness теперь должно пройти нормально и позволить остальным проверкам
        // проверять то, что они там проверяют. 
        $emptyError = $action->hasPostError('username', 'emptiness');
        $minLengthError = $action->hasPostError('username', 'min-length');
        $this->assertTrue(!$emptyError && $minLengthError);
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefaultValue()
    {
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');

        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setValidationConfig($config);

        $this->assertEquals('Doctor Who', $action->getFieldDefault('post', 'alter', false));
        $this->assertEquals('TARDIS', $action->getFieldDefault('post', 'alter', true));
        $this->assertEquals('Dalek', $action->getFieldDefault('post', 'enemy', false));
        $this->assertEquals('Dalek', $action->getFieldDefault('post', 'enemy', true));
        $this->assertEquals(null, $action->getFieldDefault('post', 'true-name', false));
        $this->assertEquals('', $action->getFieldDefault('post', 'true-name', true));
    }

    /**
     * @runInSeparateProcess
     */
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
        $action->setValidationConfig($config);

        $action->setPostOne('username', 'BadUser');
        $action->setPostOne('empty-field', '');
        $action->setPostOne('question', '');
        
        $this->assertEquals('BadUser', $action->getField('post', 'username'));
        $this->assertEquals('', $action->getField('post', 'empty-field'));
        $this->assertEquals(null, $action->getField('post', 'non-existence-field'));
        $this->assertEquals(42, $action->getField('post', 'answer'));
        $this->assertEquals('...', $action->getField('post', 'question'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testFailedRuleMayThrowException()
    {
        $config = new Json(ROOT_DIR . '/tests/config/actions/JsonValidatedAction.json');

        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setValidationConfig($config);

        $rules = new BaseActionRules;
        $action->setRule('max-length', $rules->getMaxLengthRule());

        $this->expectException(RuleCheckFailedException::class);

        $action->setPostOne('username', 'Kostyak');
        $action->exec();
    }

    /**
     * @runInSeparateProcess
     */
    public function testInnerInterData()
    {
        $action = new UserDeleteAction([], '');
        $config = new Json(ROOT_DIR . '/tests/config/actions/UserDeleteAction.json');
        $action->setValidationConfig($config);

        // В этом тестовом экшне id = 1 является единственным путем успешно 
        // пройти проверки.
        $action->setPostOne('id', 1);

        // В теле экшна используются промежуточные данные. Если их нет, будет ошибка.
        $action->exec();

        $this->assertTrue($action->isSuccess());
    }
}