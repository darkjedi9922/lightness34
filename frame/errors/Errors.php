<?php namespace frame\errors;

use frame\core\Driver;
use frame\events\Events;
use frame\tools\Debug;
use frame\config\ConfigRouter;
use frame\errors\handlers\ErrorHandler;
use frame\tools\Logger;

class Errors extends Driver
{
    /**
     * Event of any uncaught error.
     * Event args: Throwable.
     */
    const EVENT_ERROR = 'errors-component-error';

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

    public function __construct()
    {
        $this->enableErrorHandlers();
    }

    /**
     * @param string $throwableClass Имя класса Throwable исключения.
     * @param string $handlerClass Имя класса его обработчика. Обработчик
     * должен реализовывать интерфейс ErrorHandler.
     * @throws Exception Если handlerClass это не ErrorHandler.
     */
    public function setHandler($throwableClass, $handlerClass)
    {
        if (!is_subclass_of($handlerClass, ErrorHandler::class))
            throw new \Exception("Class $handlerClass is not an ErrorHandler");
        $this->handlers[$throwableClass] = $handlerClass;
    }

    /**
     * Устанавливает обработчик ошибок, на которые не был задан обработчик
     * @param string $handlerClass Имя класса обработчика.
     * @throws Exception Если handlerClass это не ErrorHandler.
     */
    public function setDefaultHandler($handlerClass)
    {
        if (!is_subclass_of($handlerClass, ErrorHandler::class))
            throw new \Exception("Class $handlerClass is not an ErrorHandler");
        $this->defaultHandler = $handlerClass;
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
            if (!($e = error_get_last())) return;
            $type = ErrorException::LEVEL_ERRORS[$e['type']] ?? null;
            if ($type === Logger::CRITICAL) {
                $this->handleError(new ErrorException(
                    $type, $e['message'], $e['file'], $e['line'])
                );
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
    public function handleError(\Throwable $e)
    {
        $coreConfig = ConfigRouter::getDriver()->findConfig('core');
        $logging = $coreConfig->{'log.enabled'};
        if ($logging) {
            $logger = Logger::getCurrent();
            $level = is_subclass_of($e, LogLevel::class) 
                ? $e->getLogLevel()
                : Logger::ERROR;
            if (in_array($level, $coreConfig->{'errors.logLevels'}))
                $logger->write($level, Debug::getErrorMessage($e));
        }

        Events::getDriver()->emit(self::EVENT_ERROR, $e);

        if (isset($this->handlers[get_class($e)])) {
            $handler = new $this->handlers[get_class($e)];
            $handler->handle($e);
        } else if ($this->defaultHandler) {
            $handler = new $this->defaultHandler;
            $handler->handle($e);
        } else throw $e;
    }
}