<?php namespace frame\handlers;

use frame\Core;
use frame\views\Page;
use frame\exceptions\StrictException;

class DefaultErrorHandler implements ErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle($error)
    {
        $errorsMode = Core::$app->config->{"errors.showMode"};
        if ($errorsMode == "errorPage" || $errorsMode == "errorDevPage") {
            $page = Core::$app->config->{"errors." . $errorsMode};
            try {
                (new Page($page))->show();
            } catch (\Exception $pe) {
                (new StrictExceptionHandler)->handle(new StrictException('Error page or error development page does not exist', 0, $error));
            }
        } else if ($errorsMode == "display") {
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