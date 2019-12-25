<?php namespace engine\statistics\stats;

use frame\database\Identity;

class EventSubscriberStat extends Identity
{
    public static function getTable(): string
    {
        return 'stat_event_subscribers';
    }
}