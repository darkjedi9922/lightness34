<?php namespace frame\tools;

use frame\tools\files\Directory;

$_isSemaphoresSupported = function_exists('sem_get');

class Semaphores
{
    private static $dir = ROOT_DIR . '/runtime/semaphores';

    /**
     * @return resource
     */
    public static function get(int $key)
    {
        global $_isSemaphoresSupported;
        if ($_isSemaphoresSupported) return sem_get($key);
        if (!Directory::exists(self::$dir)) Directory::createRecursive(self::$dir);
        return fopen(self::$dir . "/$key", 'w+');
    }

    /**
     * @param resource $semId
     */
    public static function acquire($semId, bool $nowait = false): bool
    {
        global $_isSemaphoresSupported;
        if ($_isSemaphoresSupported) return sem_acquire($semId, $nowait);
        return flock($semId, LOCK_EX | ($nowait ? LOCK_NB : 0));
    }

    /**
     * @param resource $semId
     */
    public static function release($semId): bool
    {
        global $_isSemaphoresSupported;
        if ($_isSemaphoresSupported) return sem_release($semId);
        return flock($semId, LOCK_UN);
    }

    /**
     * Возвращаемое значение равно возвращаемому значению коллбека (если оно есть).
     */
    public static function synchronize(string $key, bool $wait, callable $callback)
    {
        $sem = self::get(crc32($key));
        if (!$sem || !self::acquire($sem, !$wait)) return;
        $result = $callback();
        self::release($sem);
        return $result;
    }
}