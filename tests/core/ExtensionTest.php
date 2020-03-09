<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\route\Router;
use frame\macros\Events;
use tests\core\examples\EventsExample;

class ExtensionTest extends TestCase
{
    public function testExtendsEvents()
    {
        $app = new Core(new Router);
        $app->use(Events::class, EventsExample::class);

        $this->assertEquals(0, EventsExample::get()->counter);
        Events::get()->emit('test');
        $this->assertEquals(1, EventsExample::get()->counter);
    }
}