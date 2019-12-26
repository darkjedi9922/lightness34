<?php namespace frame;

use frame\route\Router;
use frame\views\Page;
use frame\tools\Logger;
use frame\errors\ErrorException;
use frame\errors\HttpError;
use frame\config\Config;
use frame\modules\Module;
use frame\views\DynamicPage;
use frame\macros\EventManager;

class Core
{
    const EVENT_APP_START = 'core-app-started';
    const EVENT_APP_END = 'core-app-end';

    /**
     * Event of any uncaught error.
     * Event args: Throwable.
     */
    const EVENT_APP_ERROR = 'core-app-error';

    /**
     * Происходит при подписке нового макроса на событие. В обработчик передается
     * string имя события и callable макрос.
     */
    const META_APP_EVENT_SUBSCRIBE = '_app-macro-subscribed';

    /**
     * Происходит после обработки события. В обработчик передается string имя 
     * выполнившегося события, массив параметров события и массив callable макросов,
     * что были выполнены. Если никаких обработчиков нет, массив макросов будет пуст.
     */
    const META_APP_EVENT_EMIT = '_app-event-emit-and-handle';

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

    public function __construct(Router $router)
    {
        date_default_timezone_set('Europe/Kiev');

        $this->enableErrorHandlers();
        $this->events = new EventManager;
        $this->config = \frame\cash\config::get('core');
        $this->router = $router;
        static::$app = $this;
    }

    public function __destruct()
    {
        try {
            if ($this->events->getEmitCount(self::EVENT_APP_START) !== 0)
                $this->emit(self::EVENT_APP_END);
        } catch (\Throwable $error) {
            $this->handleError($error);
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
    public function on(string $event, callable $macro)
    {
        $this->events->subscribe($event, $macro);
        // Не сигнализируем о подписке на мета-событие.
        // if (($event[0] ?? '') === '_') return;
        $this->events->emit(self::META_APP_EVENT_SUBSCRIBE, $event, $macro);
    }

    /**
     * Вызывает сигнал о произошедшем событии приложения. События могут вызываться 
     * любые в пределах всего приложения. Важно лишь, чтобы они не совпали по имени.
     */
    public function emit(string $event, ...$args)
    {
        $macros = $this->events->emit($event, ...$args);
        // Не сигнализируем о вызове мета-события.
        // if (($event[0] ?? '') === '_') return;
        $this->events->emit(self::META_APP_EVENT_EMIT, $event, $args, $macros);
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
        $this->emit(self::EVENT_APP_START);
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

        $this->emit(self::EVENT_APP_ERROR, $e);

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