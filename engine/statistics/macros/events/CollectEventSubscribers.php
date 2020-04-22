<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventSubscriberStat;
use SplObjectStorage;

class CollectEventSubscribers extends BaseStatCollector
{
    /**
     * Key: callable macro.
     * Value: EventSubscriberStat.
     */
    private $subscriberStats;

    public function __construct()
    {
        $this->subscriberStats = new SplObjectStorage;
    }

    /**
     * Key: callable macro
     * Value: EventSubscriberStat
     */
    public function getSubscriberStats(): SplObjectStorage
    {
        return $this->subscriberStats;
    }

    protected function collect(...$args)
    {
        $event = $args[0];
        $macro = $args[1];

        // Не собираем статистику о статистике.
        if ($macro instanceof BaseStatCollector) return;
        
        $subscriber = new EventSubscriberStat;
        $subscriber->event = $event;
        $subscriber->class = str_replace('\\', '\\\\', get_class($macro));

        $this->subscriberStats[$macro] = $subscriber;
    }
}