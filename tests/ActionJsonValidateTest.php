<?php

use PHPUnit\Framework\TestCase;
use tests\engine\JsonValidatedAction;
use frame\actions\rules\BaseActionRules;
use frame\tools\Json;
use frame\actions\NoRuleError;
use frame\Action;
use frame\actions\RuleCheckFailedException;

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

        $config = new Json(ROOT_DIR . '/tests/config/actions/validating.json');
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
        $config = new Json(ROOT_DIR . '/tests/config/actions/validating.json');
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
        $config = new Json(ROOT_DIR . '/tests/config/actions/validating.json');

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
        $config = new Json(ROOT_DIR . '/tests/config/actions/validating.json');

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
    public function testFailedRuleMayThrowException()
    {
        $config = new Json(ROOT_DIR . '/tests/config/actions/validating.json');

        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);
        $action->setValidationConfig($config);

        $rules = new BaseActionRules;
        $action->setRule('max-length', $rules->getMaxLengthRule());

        $this->expectException(RuleCheckFailedException::class);

        $action->setPostOne('username', 'Kostyak');
        $action->exec();
    }
}