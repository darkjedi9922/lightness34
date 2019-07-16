<?php namespace frame\errors\handlers;

use frame\Core;
use frame\views\Page;
use frame\errors\StrictException;

use function lightlib\ob_end_clean_all;

class DefaultErrorHandler implements ErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle($error)
    {
        $page = Core::$app->config->{'errors.errorPage'};
        if ($page !== null) {
            try {
                (new Page($page))->show();
            } catch (\Exception $pe) {
                (new StrictExceptionHandler)->handle(new StrictException(
                    'Error page or error development page does not exist',
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
            exit;
        }
    }
}