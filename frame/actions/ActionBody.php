<?php namespace frame\actions;

/**
 * Ошибки, возникшие во время обработки, определены константами
 * вида E_NAME_OF_ERROR.
 */
abstract class ActionBody
{
    const DEFAULT_REDIRECT = '__DEFAULT_REDIRECT__';

    /**
     * Declares the list of get parameters that Action required. If a parameter
     * listed in this method is not set when executing an action, then an error
     * HttpError:NOT_FOUND raised.
     * 
     * Returns an array of the form ['param_name' => BaseField::class].
     * All derived from BaseField classes also can be used here.
     */
    public function listGet(): array { return []; }

    /**
     * The same as listGet() but for the post data.
     */
    public function listPost(): array { return []; }

    /**
     * The same as listGet() but for the files data.
     * For files only FileField and its derived classes can be used.
     */
    public function listFiles(): array { return []; }

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