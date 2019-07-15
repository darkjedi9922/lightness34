<?php namespace frame;

use frame\route\Router;
use frame\route\Request;
use frame\views\Page;
use frame\database\Database;
use frame\config\Json;
use frame\config\DefaultedConfig;
use frame\tools\Logger;
use frame\errors\ErrorException;
use frame\errors\HttpError;

class Core
{
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
     * @var DefaultedConfig Конфиг core.json
     */
    public $config;

    /**
     * @var array Ключ - имя класса исключения, 
     * значение - имя класса обработчика
     */
    private $hanlders = [];

    /**
     * @var string Имя класса, обрабатывающего ошибки,
     * на которые не был задан обработчик
     */
    private $defaultHandler = null;

    /**
     * @var array Ключ - команда (GET-параметр) макроса,
     * значение - имя класса действия макроса.
     */
    private $macros = [];

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

        $this->enableErrorHundlers();

        $mainConfig = new Json('config/core.json');
        $defaultConfig = new Json('config/default/core.json');
        $this->config = new DefaultedConfig($mainConfig, $defaultConfig);

        $this->router = new Router(Request::getRequest());

        static::$app = $this;
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
        $this->hanlders[$throwableClass] = $handlerClass;
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
     * @param string $name Ключ - команда (GET-параметр) макроса
     * @param string $macro Имя класса действия макроса
     */
    public function setMacro($name, $macro)
    {
        $this->macros[$name] = $macro;
    }

    public function exec()
    {
        $this->execMacros();
        $page = $this->router->pagename;
        if (Page::find($page)) (new Page($page))->show();
        else throw new HttpError(404, 'Page ' . $page . ' does not exist.');
    }

    private function execMacros()
    {
        foreach ($this->router->args as $key => $value) {
            if (isset($this->macros[$key])) {
                (new $this->macros[$key])->exec($value);
            }
        }
    }

    /**
     * Регистрирует все обработчики ошибок, где они преобразуют ошибки в Throwable
     * и делегируют их методу handleError()
     */
    private function enableErrorHundlers()
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

        if (isset($this->hanlders[get_class($e)])) (new $this->hanlders[get_class($e)])->handle($e);
        else if ($this->defaultHandler) (new $this->defaultHandler)->handle($e);
    }
}