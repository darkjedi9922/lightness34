<?php namespace engine\users\macros;

use engine\users\User;
use frame\cash\database;
use frame\database\Records;
use frame\events\DaemonMacro;

class UpdateOfflineUsers extends DaemonMacro
{
    private $intervalInSeconds = 60 * 5;

    public function __construct()
    {
        parent::__construct($this->intervalInSeconds);
    }

    protected function execDaemon()
    {
        $theIntervalTimeAgo = time() - $this->intervalInSeconds;
        database::get()->query(
            "UPDATE users SET online = 0 
            WHERE online = 1 AND last_online_time <= $theIntervalTimeAgo"
        );
    }
}