<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventRouteStat;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventEmitStat;

class EndCollectEvents extends BaseStatCollector
{
    private $routeStat;
    private $subscriberCollector;
    private $emitCollector;

    public function __construct(
        EventRouteStat $routeStat,
        CollectEventSubscribers $subscriberCollector,
        CollectEventEmits $emitCollector
    ) {
        $this->routeStat = $routeStat;
        $this->subscriberCollector = $subscriberCollector;
        $this->emitCollector = $emitCollector;
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

        $emits = $this->emitCollector->getEmits();
        for ($i = 0, $c = count($emits); $i < $c; ++$i) {
            /** @var EventEmitStat $emitStat */
            $emitStat = $emits[$i][0];
            $emitStat->route_id = $routeId;
            $emitStat->insert();
        }
    }
}