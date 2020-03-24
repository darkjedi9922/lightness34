<?php namespace frame\stdlib\configs;

use frame\config\NamedConfig;
use frame\errors\NotSupportedException;

/**
 * {@inheritdoc}
 * Конфиг PHP возвращает массив.
 * Имя конфига в методах это путь к файлу конфига (с расширением).
 */
class PhpConfig extends NamedConfig
{
    public static function exists(string $name): bool
    {
        return file_exists($name);
    }

    protected function loadConfig(): array
    {
        if (!self::exists($this->getName())) return [];
        return require $this->getName();    
    }

    protected function saveConfig()
    {
        throw new NotSupportedException;
    }
}