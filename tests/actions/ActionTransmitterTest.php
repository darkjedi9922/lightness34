<?php

use PHPUnit\Framework\TestCase;
use tests\actions\examples\ValidatedActionExample;
use frame\actions\ActionTransmitter;
use frame\actions\Action;
use tests\examples\actions\PostListActionExample;
use tests\actions\examples\PasswordActionExample;

/**
 * @runTestsInSeparateProcesses
 */
class ActionTransmitterTest extends TestCase
{
    public function testSavesAndLoadsErrors()
    {
        $transmitter = new ActionTransmitter;
        $srcAction = new Action(new ValidatedActionExample);
        $srcAction->setToken($srcAction->getExpectedToken());
        $srcAction->setData('post', 'name', '_some_invalid_value');
        $srcAction->exec();
        
        $transmitter->save($srcAction);
        $destAction = $transmitter->load(ValidatedActionExample::class);

        $this->assertTrue($destAction->hasError(ValidatedActionExample::E_INVALID));
    }

    public function testSavesOnlyListedPostFields()
    {
        $srcAction = new Action(new PostListActionExample);
        $srcAction->setDataAll('post', [
            'sum' => 7,
            'product' => '14'
        ]);

        $transmitter = new ActionTransmitter;
        $transmitter->save($srcAction);

        $destAction = $transmitter->load(PostListActionExample::class);
        $this->assertEquals(7, $destAction->getData('post', 'sum'));
        $this->assertNull($destAction->getData('post', 'product'));
    }

    public function testDoesNotSaveListedPostPasswordFields()
    {
        $srcAction = new Action(new PasswordActionExample);
        $srcAction->setDataAll('post', [
            'login' => 'Admin',
            'password' => '0000' // This is a POST_PASSWORD in the action body
        ]);

        $transmitter = new ActionTransmitter;
        $transmitter->save($srcAction);

        $destAction = $transmitter->load(PasswordActionExample::class);
        $this->assertEquals('Admin', $destAction->getData('post', 'login'));
        $this->assertNull($destAction->getData('post', 'password'));
    }
}