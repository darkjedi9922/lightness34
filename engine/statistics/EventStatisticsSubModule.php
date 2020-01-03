<?php namespace engine\statistics;

use frame\Core;
use engine\statistics\stats\EventRouteStat;
use engine\statistics\stats\EventEmitStat;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\macros\events\CollectEventSubscribers;
use engine\statistics\macros\events\CollectEventRoute;
use engine\statistics\macros\events\CollectEventEmits;
use engine\statistics\macros\events\StartCollectHandles;
use engine\statistics\macros\events\EndCollectEvents;
use frame\macros\EventManager;
use frame\database\Records;
use frame\modules\Module;

class EventStatisticsSubModule extends BaseStatisticsSubModule
{
    private $routeStat;
    private $subsciberCollector;
    private $emitCollector;
    private $startHandleCollector;

    public function __construct(string $name, Module $parent = null)
    {
        $this->routeStat = new EventRouteStat;
        $this->subsciberCollector = new CollectEventSubscribers($this->routeStat);
        $this->emitCollector = new CollectEventEmits;
        $this->startHandleCollector = new StartCollectHandles;

        // Сделаем time = null, чтобы потом проверять запускалась ли вообще сборка
        // статистики событий.
        $this->routeStat->time = null;

        parent::__construct($name, $parent);
    }

    public function __destruct()
    {
        // Если time === null, значит сборка статистики событий и не запускалась.
        if ($this->routeStat->time !== null) {
            (new EndCollectEvents(
                $this->routeStat, 
                $this->subsciberCollector, 
                $this->emitCollector,
                $this->startHandleCollector
            ))->exec();
        }
    }

    public function clearStats()
    {
        Records::select('stat_event_emit_handles')->delete();
        Records::select(EventEmitStat::getTable())->delete();
        Records::select(EventSubscriberStat::getTable())->delete();
        Records::select(EventRouteStat::getTable())->delete();
    }

    protected function getAppEventHandlers(): array
    {
        $this->collectAlreadySubscribers($this->subsciberCollector);

        return [
            EventManager::BLOCK_EVENT_SUBSCRIBE => $this->subsciberCollector,
            EventManager::BLOCK_EVENT_EMIT => $this->emitCollector,
            EventManager::BLOCK_EVENT_MACRO_START => $this->startHandleCollector,
            Core::EVENT_APP_START => new CollectEventRoute($this->routeStat),
        ];
    }

    private function collectAlreadySubscribers(CollectEventSubscribers $collector)
    {
        $subscribers = Core::$app->getEventManager()->getSubscribers();
        foreach ($subscribers as $event => $eventSubscribers) {
            foreach ($eventSubscribers as $subscriber) {
                $collector->exec($event, $subscriber);
            }
        }
    }
}