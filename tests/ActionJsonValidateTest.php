<?php

use PHPUnit\Framework\TestCase;
use tests\engine\JsonValidatedAction;
use frame\actions\rules\BaseActionRules;

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
        $action = JsonValidatedAction::instance();

        // Методы классов ActionRules возвращают callback-функции, проверяющие
        // переданные в них значения.
        $rules = new BaseActionRules();

        // addValidationRule устанавливает callback-функцию на ключевое слово 
        // правила, которое можно затем использовать в json-валидации.
        $action->setRule('mandatory', $rules->getMandatoryRule());

        $file = ROOT_DIR . '/tests/config/actions/validating.json';
        $action->setValidationFile($file);

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
        $action = JsonValidatedAction::instance();

        // Методы классов ActionRules возвращают callback-функции, проверяющие
        // переданные в них значения.
        $rules = new BaseActionRules();

        $file = ROOT_DIR . '/tests/config/actions/validating.json';
        $action->setValidationFile($file);

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
}