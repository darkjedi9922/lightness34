<?php namespace frame\stdlib\configs;

use frame\config\FileConfig;

class JsonConfig extends FileConfig
{
    public static function getFileFormat(): string
    {
        return 'json';
    }

    protected function loadConfig(): array
    {
        if (!self::exists($this->getName())) return [];
        return json_decode(file_get_contents($this->getFile()), true);
    }

    protected function saveConfig()
    {
        file_put_contents($this->getFile(), json_encode(
            $this->getData(),
            JSON_PRETTY_PRINT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES
        ));
    }
}