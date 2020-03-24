<?php namespace frame\events;

use frame\stdlib\configs\JsonConfig;
use frame\tools\files\Directory;

abstract class DaemonMacro extends Macro
{
    /** @var JsonConfig */
    private static $config = null;
    private $interval;

    public function __construct(int $intervalInSeconds)
    {
        $this->interval = $intervalInSeconds;
    }

    public function exec(...$args)
    {
        $sem = sem_get(crc32('daemons-times'));
        if (!$sem || !sem_acquire($sem, true)) return;
        
        $config = $this->getTimesConfig();
        $lastExecTime = $config->get(static::class) ?? 0;
        $itIsTimeToRun = $lastExecTime < time() - $this->interval;
        if ($itIsTimeToRun) {
            $this->execDaemon();
            $config->set(static::class, time());
            $config->save();
        }

        sem_release($sem);
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

    private function getTimesConfig(): JsonConfig
    {
        if (!self::$config) {
            $daemonsFolder = $this->getRuntimeFolder();
            self::$config = new JsonConfig("$daemonsFolder/times.json");
            if (!Directory::exists($daemonsFolder))
                Directory::createRecursive($daemonsFolder);
        }
        return self::$config;
    }
}