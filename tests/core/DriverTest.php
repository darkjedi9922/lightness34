<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\core\DriverNotSetupException;
use frame\events\Events;
use tests\core\examples\EventsDriverExample;
use tests\core\examples\AbstractDriverExample;
use function lightlib\remove_prefix;

/**
 * @runTestsInSeparateProcesses
 */
class DriverTest extends TestCase
{
    private static $thisFile;

    public static function setUpBeforeClass(): void
    {
        self::$thisFile = ltrim(remove_prefix(__FILE__, ROOT_DIR), '\\/');
    }

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

    public function testThrowsDriverExceptionIfItIsAbstractAndNotReplacedFromCore()
    {
        $app = new Core;
        $driverClass = AbstractDriverExample::class;

        $this->expectException(DriverNotSetupException::class);

        try {
            $app->getDriver($driverClass);
        } catch (DriverNotSetupException $e) {
            $this->assertEquals(self::$thisFile, $e->getCallerFile());
            $this->assertEquals($driverClass, $e->getRequiredDriverClass());
            throw $e;
        }
    }

    public function testThrowsDriverExceptionIfItIsAbstractAndNotReplacedFromDriver()
    {
        $app = new Core;
        $driverClass = AbstractDriverExample::class;

        $this->expectException(DriverNotSetupException::class);

        try {
            $driverClass::getDriver();
        } catch (DriverNotSetupException $e) {
            $this->assertEquals(self::$thisFile, $e->getCallerFile());
            $this->assertEquals($driverClass, $e->getRequiredDriverClass());
            throw $e;
        }
    }
}