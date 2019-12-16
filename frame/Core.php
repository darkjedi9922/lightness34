<?php namespace frame;

use frame\route\Router;
use frame\route\Request;
use frame\views\Page;
use frame\tools\Logger;
use frame\errors\ErrorException;
use frame\errors\HttpError;
use frame\config\Config;
use frame\modules\Module;
use frame\views\DynamicPage;
use frame\macros\EventManager;
use frame\macros\Macro;

class Core
{
    const EVENT_APP_START = 'core-app-started';
    const EVENT_APP_END = 'core-app-end';

    /**
     * @var Core $app Экземпляр приложения. Инициализуется
     * при инициализации экземпляра Core
     */
    public static $app = null;

    /**
     * @var Router Роутер текущего запроса
     */
    public $router;

    /**
     * @var Config Конфиг core.json
     */
    public $config;

    /**
     * @var array Ключ - имя класса исключения, 
     * значение - имя класса обработчика
     */
    private $handlers = [];

    /**
     * @var string Имя класса, обрабатывающего ошибки,
     * на которые не был задан обработчик
     */
    private $defaultHandler = null;

    private $modules = [];

    private $events = null;

    /**
     * Конструктор
     */
    public function __construct()
    {
        ob_start(); // Чтобы можно было стереть весь предыдущий вывод видов и вывести что-то вместо него

        mb_internal_encoding('UTF-8');
        define('NONE', -1);
        define('endl', '<br>');
        define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
        date_default_timezone_set('Europe/Kiev');

        $this->enableErrorHandlers();
        $this->events = new EventManager;
        $this->config = \frame\cash\config::get('core');
        $this->router = new Router(Request::getRequest());
        static::$app = $this;
    }

    public function __destruct()
    {
        if ($this->events->getEmitCount(self::EVENT_APP_START) !== 0)
            $this->events->emit(self::EVENT_APP_END);
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
     * @param string $throwableClass Имя класса Throwable исключения
     * @param string $handlerClass Имя класса его обработчика. Обработчик
     * должен реализовывать интерфейс ErrorHandler
     */
    public function setHandler($throwableClass, $handlerClass)
    {
        $this->handlers[$throwableClass] = $handlerClass;
    }

    /**
     * Устанавливает обработчик ошибок, на которые не был задан обработчик
     * @param string $handlerClass Имя класса обработчика
     */
    public function setDefaultHandler($handlerClass)
    {
        $this->defaultHandler = $handlerClass;
    }

    /**
     * Устанавливает обработчик на любое событие, которое вызывается через 
     * Core::emit(). События могут устаналиваться любые в пределах всего приложения.
     * Важно лишь, чтобы они не совпали по имени. 
     */
    public function on(string $event, Macro $handler)
    {
        $this->events->subscribe($event, $handler);
    }

    /**
     * Вызывает сигнал о произошедшем событии приложения. События могут вызываться 
     * любые в пределах всего приложения. Важно лишь, чтобы они не совпали по имени.
     */
    public function emit(string $event)
    {
        $this->events->emit($event);
    }

    /**
     * Менеджер событий, через который работает механизм событий в экземпляре класса.
     * Из него можно узнать дополнительную информацию о работе событий.
     */
    public function getEventManager(): EventManager
    {
        return $this->events;
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
        $this->events->emit(self::EVENT_APP_START);
        $pagename = $this->router->pagename;
        $page = $this->findPage($pagename);
        if ($page) $page->show();
        else throw new HttpError(404, 'Page ' . $pagename . ' does not exist.');
    }

    /**
     * Регистрирует все обработчики ошибок, где они преобразуют ошибки в Throwable
     * и делегируют их методу handleError()
     */
    private function enableErrorHandlers()
    {
        // error
        set_error_handler(function ($type, $message, $file, $line) {
            $this->handleError(new ErrorException($type, $message, $file, $line));
        });
        // shutdown error
        register_shutdown_function(function () {
            $e = error_get_last();
            if ($e && in_array($e['type'], ErrorException::FATAL_ERRORS)) {
                $this->handleError(new ErrorException($e['type'], $e['message'], $e['file'], $e['line']));
            }
        });
        // exceptions
        set_exception_handler(function (\Throwable $e) {
            $this->handleError($e);
        });
    }

    /**
     * Абсолютно все необработанные ошибки и исключения всех видов
     * и уровней попадают сюда в виде Throwable
     */
    private function handleError(\Throwable $e)
    {
        $logging = $this->config->{'log.enabled'};
        if ($logging) $this->writeInLog(Logger::ERROR, $e);

        if (isset($this->handlers[get_class($e)])) (new $this->handlers[get_class($e)])->handle($e);
        else if ($this->defaultHandler) (new $this->defaultHandler)->handle($e);
        else throw $e;
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