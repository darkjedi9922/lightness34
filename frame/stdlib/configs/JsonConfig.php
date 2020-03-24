<?php namespace frame\stdlib\configs;

use frame\config\NamedConfig;

/**
 * {@inheritdoc}
 * Имя конфига в методах это путь к файлу конфига (с расширением).
 */
class JsonConfig extends NamedConfig
{
    public static function exists(string $name): bool
    {
        return file_exists($name);
    }

    protected function loadConfig(): array
    {
        if (!self::exists($this->getName())) return [];
        return json_decode(file_get_contents($this->getName()), true);
    }

    protected function saveConfig()
    {
        file_put_contents($this->getName(), json_encode(
            $this->getData(),
            JSON_PRETTY_PRINT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES
        ));
    }
}