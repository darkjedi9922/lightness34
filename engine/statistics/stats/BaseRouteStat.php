<?php namespace engine\statistics\stats;

use frame\stdlib\cash\router;
use frame\database\Identity;

abstract class BaseRouteStat extends Identity
{
    public abstract static function getTable(): string;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function collectCurrent()
    {
        $this->route = router::get()->pagename;
        $this->time = time();
    }
}