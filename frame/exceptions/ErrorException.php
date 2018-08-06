<?php namespace frame\exceptions;

class ErrorException extends \ErrorException
{
    /**
     * Ключи являются числовыми константами, а значения - именами констант
     */
    const TYPES = [
        E_ERROR => 'E_ERROR',
        E_WARNING => 'E_WARNING',
        E_PARSE => 'E_PARSE',
        E_NOTICE => 'E_NOTICE',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_STRICT => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED => 'E_DEPRECATED'
    ];

    const FATAL_ERRORS = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];

    /**
     * @param int $type
     * @param string $message
     * @param string $file
     * @param int $line
     * @param \Exception $previous
     */
    public function __construct($type, $message, $file, $line, $previous = null)
    {
        parent::__construct($message, 0, $type, $file, $line, $previous);
    }
}