<?php namespace frame\core;

use frame\events\Events;

class Core
{
    const EVENT_APP_START = 'core-app-started';
    const EVENT_APP_END = 'core-app-end';

    /**
     * @var Core $app Экземпляр приложения. Инициализуется
     * при инициализации экземпляра Core
     */
    public static $app = null;

    private $uses = [];
    private $executed = false;

    public function __construct()
    { 
        // Должно находится в самом начале т.к. последующие действия могут в своей
        // реализации обращаться в Core::$app.
        static::$app = $this;

        date_default_timezone_set('Europe/Kiev');
    }

    public function __destruct()
    {
        try {
            if ($this->executed) Events::get()->emit(self::EVENT_APP_END);
        } catch (\Throwable $error) {
            $this->handleError($error);
        }
    }

    public function replaceDriver(string $driverClass, string $newDriverClass)
    {
        $this->uses[$driverClass] = [$newDriverClass];
    }

    public function decorateDriver(string $driverClass, string $decoratorClass)
    {
        $use = $this->uses[$driverClass] ?? [];
        if (is_object($use)) {
            $this->uses[$driverClass] = new $decoratorClass($use);
        } else {
            $use[] = $decoratorClass;
            $this->uses[$driverClass] = $use;
        } 
    }

    public function getDriver(string $driverClass): object
    {
        $use = $this->uses[$driverClass] ?? [];
        // Элементом $use может быть либо строка с классом, либо уже готовый
        // экземпляр (объект) этого использования, либо null.
        if (is_object($use)) return $use;
        else if (empty($use)) {
            // Тут создаем экземпляр драйвера.
            $this->uses[$driverClass] = new $driverClass;
            return $this->uses[$driverClass];
        } else {
            // При i = 0 это Driver.
            $object = $this->getDriver($use[0]);
            for ($i = 1, $c = count($use); $i < $c; ++$i) {
                // Оборачиваем в декораторы (i > 0).
                $object = new $use[0]($object);
            }
            return $object;
        }
    }

    public function exec()
    {
        $this->executed = true;
        Events::get()->emit(self::EVENT_APP_START);
    }
}