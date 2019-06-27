<?php namespace engine;

use frame\actions\Action;

class HelloAction extends Action
{
    protected function succeed()
    {
        echo 'Hello Action';
        exit;
    }
}