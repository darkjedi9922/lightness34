<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\route\Router;
use frame\macros\Events;
use tests\core\examples\EventsDecoratorExample;

/**
 * @runTestsInSeparateProcesses
 */
class DecoratorTest extends TestCase
{
    public function testDecoratesDriverInRuntime()
    {
        $counter = 0;
        $app = new Core(new Router);
        Events::get()->on('test', function() use (&$counter) {
            $counter += 1;
        });

        Events::get()->emit('test');
        $this->assertEquals(1, $counter);
        $this->assertEquals(0, EventsDecoratorExample::$counter);

        $app->decorateDriver(Events::class, EventsDecoratorExample::class);

        Events::get()->emit('test');
        $this->assertEquals(2, $counter);
        $this->assertEquals(1, EventsDecoratorExample::$counter);
    }
}