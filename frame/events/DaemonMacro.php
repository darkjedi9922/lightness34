<?php namespace frame\events;

use frame\config\Json;
use frame\tools\files\Directory;

abstract class DaemonMacro extends Macro
{
    private static $semaphore = null;
    private static $doRun = false;
    private static $config;

    private $interval;
    private $itIsTimeToRun = false;

    public function __construct(int $intervalInSeconds)
    {
        $this->interval = $intervalInSeconds;

        if (self::$semaphore === null) {
            // После завершения процесса семафор будет autoreleased.
            self::$semaphore = sem_get(crc32('daemons-times'));
            self::$doRun = self::$semaphore && sem_acquire(self::$semaphore, true);
            if (!self::$doRun) return;
            
            $daemonsFolder = $this->getRuntimeFolder();
            self::$config = new Json("$daemonsFolder/times.json");
            if (!Directory::exists($daemonsFolder))
                Directory::createRecursive($daemonsFolder);
        }
    }

    public function __destruct()
    {
        if ($this->itIsTimeToRun && self::$config) {
            self::$config->save();
            self::$config = null;
            // После этого семафор будет released.
        }
    }

    public function exec(...$args)
    {
        if (!self::$doRun) return;
        $lastExecTime = self::$config->get(static::class) ?? 0;
        $this->itIsTimeToRun = $lastExecTime < time() - $this->interval;
        if ($this->itIsTimeToRun) {
            $this->execDaemon();
            self::$config->set(static::class, time());
        }
    }

    protected abstract function execDaemon();

    /**
     * By default it doesn't need overriding.
     * It is used for tests.
     */
    protected function getRuntimeFolder(): string
    {
        return ROOT_DIR . '/runtime/daemons';
    }
}