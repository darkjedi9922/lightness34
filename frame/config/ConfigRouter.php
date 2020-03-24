<?php namespace frame\config;

/**
 * Используется для нахождения конфигов, только по их имени
 * без пути к ним и расширения.
 */
class ConfigRouter extends \frame\core\Driver
{
    private $supported = [];

    /**
     * Добавляет в порядке уменьшения приоритетности.
     * Чем раньше конфиг был добавлен, тем он приоритетнее.
     * @param string|array $namedConfigClass Имя класса или массив имен классов.
     */
    public function addSupport($namedConfigClass)
    {
        if (is_array($namedConfigClass))
            foreach ($namedConfigClass as $class)
                $this->supported[] = $class;
        else $this->supported[] = $namedConfigClass;
    }

    /**
     * @param string $name Имя конфига в директории конфигов без расширения.
     * Например, для конфига 'configs/core.json' имя 'core'.
     * 
     * Ищет только среди поддерживаемых конфигов.
     * @see addSupport
     */
    public function findConfig(string $name): ?NamedConfig
    {
        $name = "{$this->getConfigsDir()}/$name";
        foreach ($this->supported as $configClass)
            if ($configClass::exists($name)) return new $configClass($name);
        return null;
    }

    /**
     * Путь к директории с конфигами приложения, без завершающего /
     */
    public function getConfigsDir(): string
    {
        return ROOT_DIR . '/config';
    }
}