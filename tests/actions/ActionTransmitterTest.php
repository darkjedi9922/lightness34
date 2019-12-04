<?php

use PHPUnit\Framework\TestCase;
use tests\actions\examples\ValidatedActionExample;
use frame\actions\ActionTransmitter;

class ActionTransmitterTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testSavesErrors()
    {
        $action = new ValidatedActionExample;
        $action->setToken($action->getExpectedToken());
        $action->setData('post', 'name', '_some_invalid_value');
        $action->exec();
        
        $transmitter = new ActionTransmitter;
        $transmitter->save($action);

        // Here action must load itself if it was saved.
        $loadAction = new ValidatedActionExample;

        $this->assertTrue($loadAction->hasError(ValidatedActionExample::E_INVALID));
    }
}