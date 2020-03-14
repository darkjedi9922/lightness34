<?php namespace frame\errors;

use frame\core\Core;
use frame\core\Component;
use frame\macros\Events;
use frame\tools\Debug;
use frame\tools\Logger;

class Errors extends Component
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
    private function handleError(\Throwable $e)
    {
        $logging = Core::$app->config->{'log.enabled'};
        if ($logging) {
            Core::$app->writeInLog(Logger::ERROR, Debug::getErrorMessage($e));
        }

        Events::get()->emit(self::EVENT_ERROR, $e);

        if (isset($this->handlers[get_class($e)])) 
            (new $this->handlers[get_class($e)])->handle($e);
        else if ($this->defaultHandler) (new $this->defaultHandler)->handle($e);
        else throw $e;
    }
}