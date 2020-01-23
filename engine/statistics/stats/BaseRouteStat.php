<?php namespace engine\statistics\stats;

use frame\database\Identity;
use frame\core\Core;

abstract class BaseRouteStat extends Identity
{
    public abstract static function getTable(): string;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function collectCurrent()
    {
        $router = Core::$app->router;
        $this->route = $router->pagename;
        $this->time = time();
    }
}