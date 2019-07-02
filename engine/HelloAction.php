<?php namespace engine;

use frame\actions\Action;

class HelloAction extends Action
{
    protected function succeed()
    {
        echo 'Hello Action ' . $this->getData(self::ARGS, self::ID) . endl;
        echo 'Recieved answer is ' . $this->getData(self::ARGS, 'answer');
        exit;
    }
}