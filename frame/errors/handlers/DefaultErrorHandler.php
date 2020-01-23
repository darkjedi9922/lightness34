<?php namespace frame\errors\handlers;

use frame\core\Core;
use frame\errors\StrictException;

use function lightlib\ob_end_clean_all;
use frame\route\Response;
use frame\errors\HttpError;
use frame\views\ErrorPage;

class DefaultErrorHandler implements ErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle($error)
    {
        if (Response::getCode() === HttpError::OK) 
            Response::setCode(HttpError::INTERNAL_SERVER_ERROR);

        $page = Core::$app->config->{'errors.errorPage'};
        if ($page !== null) {
            try {
                (new ErrorPage($page))->show();
            } catch (\Exception $pe) {
                (new StrictExceptionHandler)->handle(new StrictException(
                    'Error page does not exist',
                    0, $error
                ));
            }
        } else {
            /**
             * Все виды при своей загрузке входят в новый вложенный уровень буфера.
             * Благодаря этому при ошибке, стираем все что должно было быть выведено
             * на каждом из уровней, потом выводим ошибку и прекращаем выполнение скрипта.
             */
            ob_end_clean_all();
            echo str_replace("\n", endl, $error);
            Response::finish();
        }
    }
}