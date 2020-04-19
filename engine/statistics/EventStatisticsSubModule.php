<?php namespace engine\statistics;

use engine\statistics\stats\EventEmitStat;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\macros\events\CollectEventSubscribers;
use engine\statistics\macros\events\CollectEventEmits;
use engine\statistics\macros\events\StartCollectHandles;
use engine\statistics\macros\events\EndCollectHandles;
use engine\statistics\macros\events\EndCollectEvents;
use engine\statistics\tools\StatEvents;
use frame\events\Events;
use frame\database\Records;
use frame\modules\Module;
use engine\statistics\stats\RouteStat;

class EventStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeStat;
    private $subsciberCollector;
    private $emitCollector;
    private $startHandleCollector;
    private $endHandleCollector;

    public function __construct(
        string $name,
        RouteStat $routeStat,
        Module $parent = null
    ) {
        $this->routeStat = $routeStat;
        $this->subsciberCollector = new CollectEventSubscribers;
        $this->emitCollector = new CollectEventEmits;
        $this->startHandleCollector = new StartCollectHandles;
        $this->endHandleCollector = new EndCollectHandles(
            $this->startHandleCollector
        );

        parent::__construct($name, $parent);
    }

    public function clearStats()
    {
        Records::from('stat_event_emit_handles')->delete();
        Records::from(EventEmitStat::getTable())->delete();
        Records::from(EventSubscriberStat::getTable())->delete();
    }

    public function endCollecting()
    {
        (new EndCollectEvents(
            $this->routeStat,
            $this->subsciberCollector,
            $this->emitCollector,
            $this->startHandleCollector
        ))->exec();
    }

    public function getAppEventHandlers(): array
    {
        $this->collectAlreadySubscribers($this->subsciberCollector);

        return [
            StatEvents::EVENT_SUBSCRIBE => $this->subsciberCollector,
            StatEvents::EVENT_EMIT => $this->emitCollector,
            StatEvents::EVENT_MACRO_START => $this->startHandleCollector,
            StatEvents::EVENT_MACRO_END => $this->endHandleCollector
        ];
    }

    private function collectAlreadySubscribers(CollectEventSubscribers $collector)
    {
        $subscribers = Events::getDriver()->getSubscribers();
        foreach ($subscribers as $event => $eventSubscribers) {
            foreach ($eventSubscribers as $subscriber) {
                $collector->exec($event, $subscriber);
            }
        }
    }
}