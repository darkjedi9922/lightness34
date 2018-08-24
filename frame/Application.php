<?php namespace frame;

use frame\Router;
use frame\LatePropsObject;
use frame\http\Request;
use frame\views\Page;
use frame\database\Database;
use frame\tools\Json;
use frame\tools\Logger;
use frame\exceptions\ErrorException;
use frame\exceptions\StrictException;
use frame\exceptions\HttpError;

/**
 * Application - не только экземпляр приложения, а также это сосредоточение 
 * поведения всего приложения в различных ситуациях. Его можно переопределить
 * субклассированием.
 * @todo ?Behaviour
 * 
 * @property-read Database $db База данных
 * @property-read Action $action Текущий выполняющийся action. Если его нет, вернет null.
 */
class Application extends LatePropsObject
{
    /**
     * @var Router Роутер текущего запроса
     */
    public $router;

    /**
     * @var Json Конфиг core.json
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
        $this->config = new Json('config/core.json');
        $this->router = new Router(Request::getRequest());

        Core::$app = $this;
    }

    public function writeInLog(string $type, string $message)
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

    public function exec()
    {
        if ($this->action) $this->action->exec();
        $page = $this->router->pagename;
        if (Page::find($page)) (new Page($page))->show();
        else throw new HttpError(404, 'Page ' . $page . ' does not exist.');
    }

    protected function __create__action()
    {
        if ($action = $this->router->getArg('action')) {
            $args = http_parse_query($action, ';');
            $name = explode('_', $args['action']);
            $id = $name[0];
            $class = $name[1];
            return $class::instance($args, $id);
        } else return null;
    }

    protected function __create__db()
    {
        $host = $this->config->{'database.host'};
        $username = $this->config->{'database.username'};
        $password = $this->config->{'database.password'};
        $dbname = $this->config->{'database.dbname'};
        return new Database($host, $username, $password, $dbname);
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