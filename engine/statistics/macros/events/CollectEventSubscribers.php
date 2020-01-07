<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventRouteStat;
use SplObjectStorage;

class CollectEventSubscribers extends BaseStatCollector
{
    private $routeStat;

    /**
     * Key: callable macro.
     * Value: EventSubscriberStat.
     */
    private $subscriberStats;

    public function __construct(EventRouteStat $routeStat)
    {
        $this->routeStat = $routeStat;
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