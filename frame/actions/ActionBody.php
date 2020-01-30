<?php namespace frame\actions;

use frame\route\Request;

/**
 * Ошибки, возникшие во время обработки, определены константами
 * вида E_NAME_OF_ERROR.
 */
abstract class ActionBody
{
    /** Type of a GET field. */
    const GET_INT = 'int';
    const GET_STRING = 'string';

    /** Type of a POST field. */
    const POST_INT = 'int';
    /** Bool is usually represented by a checkbox input. */
    const POST_BOOL = 'bool';
    const POST_TEXT = 'string';
    const POST_PASSWORD = 'password';

    const DEFAULT_REDIRECT = '__DEFAULT_REDIRECT__';

    /**
     * Declares the list of get parameters that Action required. If a parameter
     * listed in this method is not set when executing an action, then an error
     * HttpError:NOT_FOUND raised.
     * 
     * Returns an array of the form ['param_name' => <GET_TYPE>]
     * The <GET_TYPE> is Action constants declaring the type of a parameter such as
     * GET_INT, GET_TEXT etc.
     */
    public function listGet(): array { return []; }

    /**
     * The same as listGet() but for the post data with POST_TYPE field types.
     */
    public function listPost(): array { return []; }

    /**
     * Is run first
     * Suggests override if it is needed
     */
    public function initialize(array $get) { }

    /**
     * Is run second
     * Returns array of error codes
     * Suggests override if it is needed
     * 
     * @return array Коды ошибок
     */
    public function validate(array $post, array $files) { return []; }

    /**
     * Is run third in case of the success.
     * 
     * @return array|null Данные, которые могут использоваться как результат экшна
     * в дальнейшем (например, для передачи его как ответ ajax-запросу при работе
     * экшна как ajax).
     */
    abstract public function succeed(array $post, array $files);

    /**
     * Is run third in case of the fail
     * 
     * @return array|null Данные, которые могут использоваться как результат экшна
     * в дальнейшем (например, для передачи его как ответ ajax-запросу при работе
     * экшна как ajax).
     */
    public function fail(array $post, array $files) { return []; }

    /**
     * Возвращает адрес веб-страницы, на которую нужно перейти после успешного
     * (без ошибок во время валидации данных) завершения экшна.
     * 
     * Если вернет null, редиректа не будет.
     */
    public function getSuccessRedirect(): ?string
    {
        return static::DEFAULT_REDIRECT;
    }

    /**
     * Возвращает адрес веб-страницы, на которую нужно перейти после неудачного
     * (с ошибками во время валидации данных) завершения экшна.
     * 
     * Если вернет null, редиректа не будет.
     */
    public function getFailRedirect(): ?string
    {
        return static::DEFAULT_REDIRECT;
    }
}