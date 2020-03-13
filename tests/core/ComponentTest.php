<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\route\Router;
use frame\macros\EventManager;
use tests\core\examples\EventsComponentExample;

/**
 * @runTestsInSeparateProcesses
 */
class ComponentTest extends TestCase
{
    public function testReplacesEvents()
    {
        $app = new Core(new Router);
        $app->replace(EventManager::class, EventsComponentExample::class);

        $this->assertEquals(0, EventsComponentExample::get()->counter);
        EventManager::get()->emit('test');
        // Во время emit происходит еще один блокирующий мета emit про emit.
        // Поэтому нужно ожидать счетчтик со значением 2.
        $this->assertEquals(2, EventsComponentExample::get()->counter);
    }
}