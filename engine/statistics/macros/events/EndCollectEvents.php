<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventRouteStat;
use engine\statistics\stats\EventSubscriberStat;

class EndCollectEvents extends BaseStatCollector
{
    private $routeStat;
    private $subscriberCollector;

    public function __construct(
        EventRouteStat $routeStat,
        CollectEventSubscriber $subscriberCollector
    ) {
        $this->routeStat = $routeStat;
        $this->subscriberCollector = $subscriberCollector;
    }

    protected function collect(...$args)
    {
        $routeId = $this->routeStat->insert();
        $subscribers = $this->subscriberCollector->getSubscribers();
        for ($i = 0, $c = count($subscribers); $i < $c; ++$i) {
            /** @var EventSubscriberStat $subscriber */
            $subscriber = $subscribers[$i][0];
            $subscriber->route_id = $routeId;
            $subscriber->insert();
        }
    }
}