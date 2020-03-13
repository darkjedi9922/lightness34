<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\route\Router;
use frame\macros\EventManager;
use tests\core\examples\EventsEngineExample;

class ExtensionTest extends TestCase
{
    public function testExtendsEvents()
    {
        $app = new Core(new Router);
        $app->use(EventManager::class, EventsEngineExample::class);

        $this->assertEquals(0, EventsEngineExample::get()->counter);
        EventManager::get()->emit('test');
        // Во время emit происходит еще один блокирующий мета emit про emit.
        // Поэтому нужно ожидать счетчтик со значением 2.
        $this->assertEquals(2, EventsEngineExample::get()->counter);
    }
}