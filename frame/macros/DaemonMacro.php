<?php namespace frame\macros;

use frame\config\Json;
use frame\tools\files\Directory;
use frame\tools\files\File;

abstract class DaemonMacro extends Macro
{
    private static $config;

    private $interval;
    private $filename;

    public function __construct(int $intervalInSeconds)
    {
        $id = crc32(static::class);
        $daemonsFolder = $this->getRuntimeFolder();
        $this->filename = "$daemonsFolder/$id";
        $this->interval = $intervalInSeconds;
        if (self::$config === null) {
            self::$config = new Json("$daemonsFolder/times.json");
            if (!Directory::exists($daemonsFolder)) {
                Directory::createRecursive($daemonsFolder);
            }
        }
    }

    public function __destruct()
    {
        if (self::$config !== null) {
            self::$config->save();
            self::$config = null;
        }
    }

    public function exec(...$args)
    {
        $lastExecTime = self::$config->get(static::class) ?? 0;
        $itIsTimeToRun = $lastExecTime < time() - $this->interval;
        if ($itIsTimeToRun && !File::exists($this->filename)) {
            File::create($this->filename);
            $this->execDaemon();
            self::$config->set(static::class, time());
            File::delete($this->filename);
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