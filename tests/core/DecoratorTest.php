<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\events\Events;
use tests\core\examples\EventsDecoratorExample;

/**
 * @runTestsInSeparateProcesses
 */
class DecoratorTest extends TestCase
{
    public function testDecoratesDriverInRuntime()
    {
        $counter = 0;
        $app = new Core;
        Events::getDriver()->on('test', function() use (&$counter) {
            $counter += 1;
        });

        Events::getDriver()->emit('test');
        $this->assertEquals(1, $counter);
        $this->assertEquals(0, EventsDecoratorExample::$counter);

        $app->decorateDriver(Events::class, EventsDecoratorExample::class);

        Events::getDriver()->emit('test');
        $this->assertEquals(2, $counter);
        $this->assertEquals(1, EventsDecoratorExample::$counter);
    }
}