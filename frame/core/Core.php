<?php namespace frame\core;

use frame\events\Events;
use frame\errors\Errors;

class Core
{
    const EVENT_APP_START = 'core-app-started';
    const EVENT_APP_END = 'core-app-end';

    /**
     * @var Core $app Экземпляр приложения. Инициализуется
     * при инициализации экземпляра Core
     */
    public static $app = null;

    private $drivers = [];
    private $decorators = [];
    private $executed = false;

    public function __construct(array $drivers = [])
    {
        // Должно находится в самом начале т.к. последующие действия могут в своей
        // реализации обращаться в Core::$app.
        static::$app = $this;

        date_default_timezone_set('Europe/Kiev');

        foreach ($drivers as $driverClass => $replacedDriverClass)
            $this->replaceDriver($driverClass, $replacedDriverClass);
    }

    public function __destruct()
    {
        try {
            if ($this->executed) Events::getDriver()->emit(self::EVENT_APP_END);
        } catch (\Throwable $error) {
            Errors::getDriver()->handleError($error); 
        }
    }

    public function replaceDriver(string $driverClass, string $newDriverClass)
    {
        $this->drivers[$driverClass] = $newDriverClass;
    }

    public function decorateDriver(string $driverClass, string $decoratorClass)
    {
        $use = $this->drivers[$driverClass] ?? [];
        if (is_object($use))
            $this->drivers[$driverClass] = new $decoratorClass($use);
        else $this->decorators[$driverClass][] = $decoratorClass;
    }

    public function getDriver(string $driverClass): object
    {
        $use = $this->drivers[$driverClass] ?? null;
        // Элементом $use может быть либо строка с классом, либо уже готовый
        // экземпляр (объект) этого использования, либо null.
        if (is_object($use)) return $use;
        
        if ($use === null) $object = new $driverClass;
        else $object = $this->getDriver($use);

        $decorators = $this->decorators[$driverClass] ?? [];
        for ($i = 0, $c = count($decorators); $i < $c; ++$i)
            $object = new $decorators[$i]($object);

        return $this->drivers[$driverClass] = $object;
    }

    public function exec()
    {
        $this->executed = true;
        Events::getDriver()->emit(self::EVENT_APP_START);
    }
}