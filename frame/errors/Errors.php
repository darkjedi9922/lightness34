<?php namespace frame\errors;

use frame\core\Driver;
use frame\events\Events;
use frame\tools\Debug;
use frame\cash\config;
use frame\cash\logger;

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
                $this->handleError(new ErrorException(
                    $e['type'], $e['message'], $e['file'], $e['line'])
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
        $logging = config::get('core')->{'log.enabled'};
        if ($logging) {
            $logger = logger::get();
            $logger->write($logger::ERROR, Debug::getErrorMessage($e));
        }

        Events::get()->emit(self::EVENT_ERROR, $e);

        if (isset($this->handlers[get_class($e)])) 
            (new $this->handlers[get_class($e)])->handle($e);
        else if ($this->defaultHandler) (new $this->defaultHandler)->handle($e);
        else throw $e;
    }
}