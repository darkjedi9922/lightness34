<?php namespace frame\config;

/**
 * {@inheritdoc}
 * Контекст использование имени определяется конкретным 
 * типом конфига. Например, это может быть путь к файлу или имя таблицы в БД.
 */
abstract class NamedConfig extends Config
{
    private $name;
    
    public abstract static function exists(string $name): bool;
    
    public function __construct(string $name)
    {
        $this->name = $name;
        parent::__construct();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     * @see getName
     */
    protected abstract function loadConfig(): array;

    /**
     * {@inheritdoc}
     * @see getName
     */
    protected abstract function saveConfig();
}