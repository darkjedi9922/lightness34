<?php

use PHPUnit\Framework\TestCase;
use tests\engine\JsonValidatedAction;
use frame\actions\rules\BaseActionRules;
use frame\tools\Json;
use frame\actions\NoRuleError;
use frame\Action;

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
    public function testCallbackValidateWithOnlyPresent()
    {
        $action = new JsonValidatedAction([], '', Action::NO_RULE_IGNORE);

        // Методы классов ActionRules возвращают callback-функции, проверяющие
        // переданные в них значения.
        $rules = new BaseActionRules();

        $config = new Json(ROOT_DIR . '/tests/config/actions/validating.json');
        $action->setValidationConfig($config);

        // Последний параметр говорит, что запускать проверку нужно только когда
        // значение было передано.
        $action->setRule('emptiness', $rules->getEmptinessRule(), true);
        $action->exec();

        // В экшн пока не было передано post значения `username`. Проверка на
        // пустоту не сработает.
        $this->assertTrue($action->isSuccess());

        // Передаем пустое значение.
        $action->setPostOne('username', '');
        $action->exec();

        // Теперь сработает проверка.
        $this->assertTrue($action->hasPostError('username', 'emptiness'));

        // Ну все таки проверим саму работу проверки...
        $action->setPostOne('username', 'Jed');
        $action->exec();
        $this->assertTrue($action->isSuccess());
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
}