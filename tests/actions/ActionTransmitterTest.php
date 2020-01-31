<?php

use PHPUnit\Framework\TestCase;
use frame\actions\ActionTransmitter;
use frame\actions\Action;
use tests\actions\examples\ValidatedActionExample;
use tests\actions\examples\PostListActionExample;
use tests\actions\examples\PasswordActionExample;
use tests\actions\examples\AlwaysSucceedActionExample;
use frame\core\Core;
use frame\route\Router;

/**
 * @runTestsInSeparateProcesses
 */
class ActionTransmitterTest extends TestCase
{
    protected function setUp(): void
    {
        $app = new Core(new Router);
    }

    public function testSavesAndLoadsErrors()
    {
        $transmitter = new ActionTransmitter;
        $srcAction = new Action(new ValidatedActionExample);
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
            'password' => '0000' // This is a PasswordField in the action body
        ]);

        $transmitter = new ActionTransmitter;
        $transmitter->save($srcAction);

        $destAction = $transmitter->load(PasswordActionExample::class);
        $this->assertEquals('Admin', $destAction->getData('post', 'login'));
        $this->assertNull($destAction->getData('post', 'password'));
    }

    public function testSavesAndLoadsTheActionResult()
    {
        $srcAction = new Action(new AlwaysSucceedActionExample);
        $srcAction->exec();

        $transmitter = new ActionTransmitter;
        $transmitter->save($srcAction);

        $destAction = $transmitter->load(AlwaysSucceedActionExample::class);
        $this->assertEquals(['resultAnswer' => 42], $destAction->getResult());
    }
}