<?php namespace engine\statistics\macros;

use frame\macros\Macro;
use frame\database\Identity;

class InsertStat implements Macro
{
    private $stat;

    public function __construct(Identity $stat)
    {
        $this->stat = $stat;
    }

    public function exec()
    {
        $this->stat->insert();
    }
}