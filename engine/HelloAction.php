<?php namespace engine;

use frame\Action;

/**
 * Логирует полученное сообщение message и возвращается обратно.
 */
class HelloAction extends Action
{
    protected function successBody($data, $files)
    {
        $message = $this->getParameter('message');
        $this->app->writeInLog('ACTION', $message);
    }
}