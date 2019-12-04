<?php

use PHPUnit\Framework\TestCase;
use tests\actions\examples\ValidatedActionExample;
use frame\actions\ActionTransmitter;

class ActionTransmitterTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testSavesAndLoadsErrors()
    {
        $transmitter = new ActionTransmitter;
        $srcAction = new ValidatedActionExample;
        $srcAction->setToken($srcAction->getExpectedToken());
        $srcAction->setData('post', 'name', '_some_invalid_value');
        $srcAction->exec();
        
        $transmitter->save($srcAction);
        $destAction = $transmitter->load(ValidatedActionExample::class);

        $this->assertTrue($destAction->hasError(ValidatedActionExample::E_INVALID));
    }
}