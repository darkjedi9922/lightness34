<?php namespace frame\stdlib\configs;

use frame\config\FileConfig;
use frame\errors\NotSupportedException;

class PhpConfig extends FileConfig
{
    public static function getFileFormat(): string
    {
        return 'php';
    }

    protected function loadConfig(): array
    {
        if (!self::exists($this->getName())) return [];
        return require $this->getFile();
    }

    protected function saveConfig()
    {
        throw new NotSupportedException;
    }
}