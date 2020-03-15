<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\route\Router;
use frame\macros\Events;
use tests\core\examples\EventsComponentExample;

/**
 * @runTestsInSeparateProcesses
 */
class ComponentTest extends TestCase
{
    public function testReplacesEvents()
    {
        $app = new Core(new Router);

        // Можно было бы Events::use(), но тогда будет подключаться класс Events,
        // который мог бы и не понадобиться.
        $app->replaceComponent(Events::class, EventsComponentExample::class);

        $this->assertEquals(0, EventsComponentExample::get()->counter);
        Events::get()->emit('test');
        $this->assertEquals(1, EventsComponentExample::get()->counter);
    }
}