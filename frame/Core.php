<?php namespace frame;

use frame\tools\Logger;
use frame\tools\Json;
use frame\Router;
use frame\exceptions\HttpError;
use frame\http\Response;
use frame\http\Request;
use frame\views\Page;
use frame\exceptions\ErrorException;
use frame\exceptions\StrictException;
use frame\LatePropsObject;

/**
 * @property-read Database $db
 */
final class Core extends LatePropsObject
{
    /**
     * @var Core $app Экземпляр приложения
     */
    public static $app = null;

    /**
     * @var array Ассоциативный массив зарегистрированных модулей.
     * Ключи - имена классов модулей, значения - экземпляры модулей. 
     */
    public static $modules = [];

    /**
     * @var Router Роутер текущего запроса
     */
    public static $router = null;

    /**
     * @var Json Конфиг core.json
     */
    public static $config = null;

    public static function exec()
    {
        static::setup();
        static::render();
    }

    public static function writeInLog(string $type, string $message)
    {
        $logger = new Logger(ROOT_DIR . '/' . self::$config->{'log.file'});
        $logger->write($type, $message);
    }

    protected function __create__db()
    {   
        $host = self::$config->{'database.host'};
        $username = self::$config->{'database.username'};
        $password = self::$config->{'database.password'};
        $dbname = self::$config->{'database.dbname'};
        return new Database($host, $username, $password, $dbname);
    }

    private function __construct() {}

    private static function setup()
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

        self::$config = new Json('config/core.json');
        self::$app = new Core;
        self::$router = new Router(Request::getRequest());
    }

    private static function render()
    {
        foreach (self::$modules as $module) $module->preparePage();
        $page = self::$router->pagename;
        if (Page::find($page)) (new Page($page))->show();
        else throw new HttpError(404, 'Page '.$page.' does not exist.');
    }

    private static function addModule(Module $module)
    {
        self::$modules[get_class($module)] = $module;
    }

    private static function setErrorHandler()
    {
        set_error_handler(function ($type, $message, $file, $line) {
            self::handleException(new ErrorException($type, $message, $file, $line));
        });
    }

    private static function registerShutdownFunction()
    {
        register_shutdown_function(function () {
            $e = error_get_last();
            if ($e && in_array($e['type'], ErrorException::FATAL_ERRORS)) {
                self::handleException(new ErrorException($e['type'], $e['message'], $e['file'], $e['line']));
            }
        });
    }

    private static function setExceptionHandler()
    {
        set_exception_handler(function (\Throwable $e) {
            self::handleException($e);
        });
    }

    /**
     * Абсолютно все необработанные ошибки и исключения всех видов
     * и уровней попадают сюда в виде Throwable
     */
    private static function handleException(\Throwable $e)
    {
        $logging = self::$config->{'log.enabled'};
        if ($logging) static::writeInLog(Logger::ERROR, $e);

        switch (get_class($e)) 
        {
            case HttpError::class:
            self::handleHttpError($e);
            break;

            case StrictException::class:
            echo 'Error has occured but ' . $e->getMessage() . ($logging ? '. See more in the log.' : '');
            break;

            default:
            $errorsMode = self::$config->{"errors.showMode"};
            if ($errorsMode == "errorPage" || $errorsMode == "errorDevPage") {
                $page = self::$config->{"errors." . $errorsMode};
                try { (new Page($page))->show(); }
                catch (\Exception $pe) { self::handleException(new StrictException('Error page or error development page does not exist', 0, $e)); }
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

    private static function handleHttpError(HttpError $e)
    {
        $page = self::$config->{'errors.' . $e->getCode() . '.page'};
        (new Page($page))->show();
    }
}