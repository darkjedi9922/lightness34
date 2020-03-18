<?php namespace frame\views;

class ViewRouter extends \frame\core\Driver
{
    public function getBaseFolder(): string
    {
        return ROOT_DIR . '/views';
    }
}