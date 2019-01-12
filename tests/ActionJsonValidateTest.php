<?php

use PHPUnit\Framework\TestCase;
use tests\engine\JsonValidatedAction;

/**
 * `Run in separate process` заглушает сообщения вида `headers already sent`, когда
 * устанавливаются куки. Используется вместо (слегка костыльной) @ заглушки.
 */
class ActionJsonValidateTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testJsonValidate()
    {
        $action = JsonValidatedAction::instance();
        $file = ROOT_DIR . '/tests/config/actions/validating.json';
        $action->setValidationFile($file);
        
        $action->exec();

        // В экшн не было передано post значения `username`.
        $this->assertTrue($action->hasPostError('username', $action::E_MANDATORY));
        $this->assertTrue($action->isFail());
    }
}