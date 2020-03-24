<?php namespace frame\config;

/**
 * {@inheritdoc}
 * 
 * Имя конфига $name в конструкторе это путь к файлу без расширения.
 */
abstract class FileConfig extends NamedConfig
{
    /**
     * {@inheritdoc}
     * @param string $name Путь к файлу без расширения.
     */
    public static function exists(string $name): bool
    {
        return file_exists("$name." . static::getFileFormat());
    }

    public abstract static function getFileFormat(): string;

    public function getFile(): string
    {
        return "{$this->getName()}.{$this->getFileFormat()}";
    }

    /**
     * {@inheritdoc}
     * @see getFile
     */
    protected abstract function loadConfig(): array;

    /**
     * {@inheritdoc}
     * @see getFile
     */
    protected abstract function saveConfig();
}