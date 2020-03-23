<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\events\Events;
use tests\core\examples\EventsDriverExample;

/**
 * @runTestsInSeparateProcesses
 */
class DriverTest extends TestCase
{
    public function testReplacesDriver()
    {
        $app = new Core;

        // Можно было бы Events::use(), но тогда будет подключаться класс Events,
        // который мог бы и не понадобиться.
        $app->replaceDriver(Events::class, EventsDriverExample::class);

        $this->assertEquals(0, EventsDriverExample::getDriver()->counter);
        Events::getDriver()->emit('test');
        $this->assertEquals(1, EventsDriverExample::getDriver()->counter);
    }
}