<?php namespace frame\tools\trackers\read;

use frame\database\Records;

class ReadStateTracker extends ReadTracker
{
    public function isReaded(): bool
    {
        return $this->getRecords()
            ->limit(1)
            ->select(['for_id'])
            ->count() !== 0;
    }

    public function setReaded()
    {
        if ($this->isReaded()) return;
        Records::from(static::TABLE)->insert();
    }
}