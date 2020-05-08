<?php
use PHPUnit\Framework\TestCase;
use frame\core\DriverAbstractClassHandler;
use frame\config\ConfigRouter;
use frame\stdlib\configs\JsonConfig;
use tests\core\examples\AbstractDriverExample;
use frame\core\DriverNotSetupException;
use frame\core\Core;
use function lightlib\remove_prefix;

/**
 * Without running in separate proccesses message "Illegal string offset
 * 'failOnRisky'" somewhy appears in the end of phpunit output. 
 * 
 * @runTestsInSeparateProcesses
 */
class DriverAbstractClassHandlerTest extends TestCase
{
    private static $thisFile;

    public static function setUpBeforeClass(): void
    {
        self::$thisFile = ltrim(remove_prefix(__FILE__, ROOT_DIR), '\\/');
    }

    public function testThrowsDriverExceptionIfItIsAbstractAndNotReplacedFromCore()
    {
        $app = new Core;
        ConfigRouter::getDriver()->addSupport(JsonConfig::class);
        $driverClass = AbstractDriverExample::class;

        $this->expectException(DriverNotSetupException::class);

        try {
            try {
                $app->getDriver($driverClass);
            } catch (Error $e) {
                (new DriverAbstractClassHandler)->handle($e);
            }
        } catch (DriverNotSetupException $e) {
            $this->assertEquals(self::$thisFile, $e->getCallerFile());
            $this->assertEquals($driverClass, $e->getRequiredDriverClass());
            throw $e;
        }
    }

    public function testThrowsDriverExceptionIfItIsAbstractAndNotReplacedFromDriver()
    {
        $app = new Core;
        ConfigRouter::getDriver()->addSupport(JsonConfig::class);
        $driverClass = AbstractDriverExample::class;

        $this->expectException(DriverNotSetupException::class);

        try {
            try {
                $driverClass::getDriver();
            } catch (Error $e) {
                (new DriverAbstractClassHandler)->handle($e);
            }
        } catch (DriverNotSetupException $e) {
            $this->assertEquals(self::$thisFile, $e->getCallerFile());
            $this->assertEquals($driverClass, $e->getRequiredDriverClass());
            throw $e;
        }
    }
}