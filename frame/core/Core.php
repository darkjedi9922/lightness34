<?php namespace frame\core;

use frame\route\Router;
use frame\views\Page;
use frame\tools\Logger;
use frame\errors\HttpError;
use frame\config\Config;
use frame\modules\Module;
use frame\views\DynamicPage;
use frame\macros\Events;

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

    /**
     * @var Router Роутер текущего запроса
     */
    public $router;

    /**
     * @var Config Конфиг core.json
     */
    public $config;

    private $modules = [];
    private $executed = false;

    public function __construct(Router $router)
    { 
        // Должно находится в самом начале т.к. последующие действия могут в своей
        // реализации обращаться в Core::$app.
        static::$app = $this;

        date_default_timezone_set('Europe/Kiev');
        $this->config = \frame\cash\config::get('core');
        $this->router = $router;
    }

    public function __destruct()
    {
        try {
            if ($this->executed) Events::get()->emit(self::EVENT_APP_END);
        } catch (\Throwable $error) {
            $this->handleError($error);
        }
    }

    public function replace(string $interface, string $class)
    {
        $this->uses[$interface] = [$class];
    }

    public function decorate(string $interface, string $class)
    {
        $use = $this->uses[$interface] ?? [];
        if (is_object($use)) $this->uses[$interface] = new $class($use);
        else {
            $use[] = $class;
            $this->uses[$interface] = $use;
        } 
    }

    public function getUseInstance(string $interface): object
    {
        $use = $this->uses[$interface] ?? [];
        // Элементом $use может быть либо строка с классом, либо уже готовый
        // экземпляр (объект) этого использования, либо null.
        if (is_object($use)) return $use;
        else if (empty($use)) {
            $this->uses[$interface] = new $interface;
            return $this->uses[$interface];
        } else {
            $object = $this->getUseInstance($use[0]);
            for ($i = 1, $c = count($use); $i < $c; ++$i) {
                $object = new $use[0]($object);
            }
            return $object;
        }
    }

    /**
     * Записывает сообщение в лог. Файл лога настраивается в конфиге фреймворка.
     * @param string $type Тип сообщения. Класс Logger содержит константы
     * с предопределенными названиями многих типов.
     * @param string $message
     */
    public function writeInLog($type, $message)
    {
        $logger = new Logger(ROOT_DIR . '/' . $this->config->{'log.file'});
        $logger->write($type, $message);
    }

    /**
     * @throws \Exception если модуль с таким именем уже существует.
     */
    public function setModule(Module $module)
    {
        if (isset($this->modules[$module->getName()])) throw new \Exception(
            "The module with name {$module->getName()} have already been added.");
        $this->modules[$module->getName()] = $module;
    }

    public function getModule(string $name): ?Module
    {
        return $this->modules[$name] ?? null;
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    public function findModule(int $id): ?Module
    {
        foreach ($this->modules as $module) {
            /** @var Module $module */
            if ($module->getId() === $id) return $module;
        }
        return null;
    }

    public function exec()
    {
        $this->executed = true;
        Events::get()->emit(self::EVENT_APP_START);
        $pagename = $this->router->pagename;
        $page = $this->findPage($pagename);
        if ($page) $page->show();
        else throw new HttpError(404, 'Page ' . $pagename . ' does not exist.');
    }

    private function findPage(string $pagename): ?Page
    {
        $parts = explode('/', $pagename);
        
        // Если в url вообще не будет задано частей страницы, то она точно не
        // динамическая т.к. для нее должно быть хотя бы одна часть url,
        // после имени динамической страницы.
        if ($pagename !== '' && DynamicPage::find('')) 
            return new DynamicPage('', $parts);

        $page = '';
        $pathCount = count($parts);
        for ($i = 0; $i < $pathCount - 1; ++$i) {
            $newPath = $page . $parts[$i];
            if (DynamicPage::find($newPath)) 
                return new DynamicPage($newPath, array_slice($parts, $i + 1));
            $page .= $parts[$i] . '/';
        }
        
        if (Page::find($pagename)) return new Page($pagename);
        return null;
    }
}