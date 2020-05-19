<?php namespace frame\errors\handlers;

use frame\errors\StrictException;
use frame\route\Response;
use frame\route\HttpError;
use frame\tools\Debug;
use frame\config\ConfigRouter;

use function lightlib\ob_restart_all;

class DefaultErrorHandler implements ErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle($error)
    {
        if (Response::getDriver()->getCode() === HttpError::OK) 
            Response::getDriver()->setCode(HttpError::INTERNAL_SERVER_ERROR);

        $page = ConfigRouter::getDriver()->findConfig('core')->{'errors.errorPage'};
        if ($page !== null) {
            try {
                (new ErrorPage($page, $error))->show();
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
            ob_restart_all();
            echo str_replace("\n", endl, Debug::getErrorMessage($error));
            Response::getDriver()->finish();
        }
    }
}