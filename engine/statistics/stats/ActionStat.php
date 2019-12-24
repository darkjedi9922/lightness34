<?php namespace engine\statistics\stats;

use frame\database\Identity;

class ActionStat extends Identity
{
    private $files = [];

    public static function getTable(): string
    {
        return 'stat_actions';
    }

    public function setHandledFiles(array $files)
    {
        $this->files = $files;
    }

    public function getHandledFiles(): array
    {
        return $this->files;
    }
}