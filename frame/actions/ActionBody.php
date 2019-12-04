<?php namespace frame\actions;

use frame\route\Request;

/**
 * Ошибки, возникшие во время обработки, определены константами
 * вида E_NAME_OF_ERROR.
 * 
 * Корректная работа checkbox:
 * <input type="hidden" name="property" value="0">
 * <input type="checkbox" name="property" value="1">
 */
abstract class ActionBody
{
    /** Type of a GET field. */
    const GET_INT = 'int';
    const GET_STRING = 'string';

    /** Type of a POST field. */
    const POST_INT = 'int';
    const POST_TEXT = 'string';
    const POST_PASSWORD = 'password';

    /**
     * Declares the list of get parameters that Action required. If a parameter
     * listed in this method is not set when executing an action, then an error
     * HttpError:NOT_FOUND raised.
     * 
     * Returns an array of the form ['param_name' => [GET_TYPE, 'description']]
     * The GET_TYPE is Action constants declaring the type of a parameter such as
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
     * Is run third in case of the success
     */
    abstract public function succeed(array $post, array $files);

    /**
     * Is run third in case of the fail
     */
    public function fail(array $post, array $files) { }

    /**
     * Определяет названия переданных post данных, которые нужно временно сохранять.
     * Используется, чтобы вывести введенные данные в форме после возвращения на 
     * страницу, например.
     * 
     * Не рекомендуется сохранять пароли и другие секретные данные.
     */
    public function getPostToSave(): array { return []; }

    /**
     * Возвращает адрес веб-страницы, на которую нужно перейти после успешного
     * (без ошибок во время валидации данных) завершения экшна.
     * 
     * Если вернет null, редиректа не будет.
     */
    public function getSuccessRedirect(): ?string
    {
        if (Request::hasReferer()) return Request::getReferer();
        else return '/';
    }

    /**
     * Возвращает адрес веб-страницы, на которую нужно перейти после неудачного
     * (с ошибками во время валидации данных) завершения экшна.
     * 
     * Если вернет null, редиректа не будет.
     */
    public function getFailRedirect(): ?string
    {
        if (Request::hasReferer()) return Request::getReferer();
        else return '/';
    }
}