<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\route\Router;
use frame\macros\EventManager;
use tests\core\examples\EventsDecoratorExample;

/**
 * @runTestsInSeparateProcesses
 */
class DecoratorTest extends TestCase
{
    public function testDecoratesEventsInRuntime()
    {
        $counter = 0;
        $app = new Core(new Router);
        EventManager::get()->on('test', function() use (&$counter) {
            $counter += 1;
        });

        EventManager::get()->emit('test');
        $this->assertEquals(1, $counter);
        $this->assertEquals(0, EventsDecoratorExample::$counter);

        $app->decorate(EventManager::class, EventsDecoratorExample::class);

        EventManager::get()->emit('test');
        $this->assertEquals(2, $counter);
        $this->assertEquals(1, EventsDecoratorExample::$counter);
    }
}