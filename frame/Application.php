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

        self::setExceptionHandler();
        self::setErrorHandler();
        self::registerShutdownFunction();

        $this->config = new Json('config/core.json');
        $this->router = new Router(Request::getRequest());

        Core::$app = $this; // ВРЕМЕННО
    }

    public function writeInLog(string $type, string $message)
    {
        $logger = new Logger(ROOT_DIR . '/' . $this->config->{'log.file'});
        $logger->write($type, $message);
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

    private function setErrorHandler()
    {
        set_error_handler(function ($type, $message, $file, $line) {
            $this->handleException(new ErrorException($type, $message, $file, $line));
        });
    }

    private function registerShutdownFunction()
    {
        register_shutdown_function(function () {
            $e = error_get_last();
            if ($e && in_array($e['type'], ErrorException::FATAL_ERRORS)) {
                $this->handleException(new ErrorException($e['type'], $e['message'], $e['file'], $e['line']));
            }
        });
    }

    private function setExceptionHandler()
    {
        set_exception_handler(function (\Throwable $e) {
            $this->handleException($e);
        });
    }

    /**
     * Абсолютно все необработанные ошибки и исключения всех видов
     * и уровней попадают сюда в виде Throwable
     */
    private function handleException(\Throwable $e)
    {
        $logging = $this->config->{'log.enabled'};
        if ($logging) $this->writeInLog(Logger::ERROR, $e);

        switch (get_class($e)) {
            case HttpError::class:
                $this->handleHttpError($e);
                break;

            case StrictException::class:
                echo 'Error has occured but ' . $e->getMessage() . ($logging ? '. See more in the log.' : '');
                break;

            default:
                $errorsMode = $this->config->{"errors.showMode"};
                if ($errorsMode == "errorPage" || $errorsMode == "errorDevPage") {
                    $page = $this->config->{"errors." . $errorsMode};
                    try {
                        (new Page($page))->show();
                    } catch (\Exception $pe) {
                        $this->handleException(new StrictException('Error page or error development page does not exist', 0, $e));
                    }
                } else if ($errorsMode == "display") {
                    /**
                     * Все виды при своей загрузке входят в новый вложенный уровень буфера.
                     * Благодаря этому при ошибке, стираем все что должно было быть выведено
                     * на каждом из уровней, потом выводим ошибку и прекращаем выполнение скрипта.
                     */
                    while (ob_get_level() > 1) ob_end_clean();
                    echo str_replace("\n", endl, $e);
                    exit;
                }
        }
    }

    private function handleHttpError(HttpError $e)
    {
        $page = $this->config->{'errors.' . $e->getCode() . '.page'};
        (new Page($page))->show();
    }
}