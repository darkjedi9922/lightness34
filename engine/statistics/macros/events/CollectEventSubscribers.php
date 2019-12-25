<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventRouteStat;

class CollectEventSubscribers extends BaseStatCollector
{
    private $routeStat;
    private $subscribers = [];

    public function __construct(EventRouteStat $routeStat)
    {
        $this->routeStat = $routeStat;
    }

    public function getSubscribers(): array
    {
        return $this->subscribers;
    }

    public function findSubscriberStat(callable $macro): ?EventSubscriberStat
    {
        for ($i = 0, $c = count($this->subscribers); $i < $c; ++$i) {
            if ($this->subscribers[1] === $macro) return $this->subscribers[0];
        }
        return null;
    }

    protected function collect(...$args)
    {
        $event = $args[0];
        $macro = $args[1];
        
        $subscriber = new EventSubscriberStat;
        $subscriber->event = $event;
        $subscriber->class = str_replace('\\', '\\\\', get_class($macro));

        $this->subscribers[] = [$subscriber, $macro];
    }
}