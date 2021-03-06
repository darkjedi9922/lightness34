<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventEmitStat;
use frame\database\Records;
use engine\statistics\stats\RouteStat;

class EndCollectEvents extends BaseStatCollector
{
    private $routeStat;
    private $subscriberCollector;
    private $emitCollector;
    private $startHandleCollector;
    private $handleCount = 0;

    public function __construct(
        RouteStat $routeStat,
        CollectEventSubscribers $subscriberCollector,
        CollectEventEmits $emitCollector,
        StartCollectHandles $startHandleCollector
    ) {
        $this->routeStat = $routeStat;
        $this->subscriberCollector = $subscriberCollector;
        $this->emitCollector = $emitCollector;
        $this->startHandleCollector = $startHandleCollector;
    }

    protected function collect(...$args)
    {
        $routeId = $this->routeStat->getId();
        $subscribers = $this->subscriberCollector->getSubscriberStats();
        $this->insertSubscribers($routeId);
        $this->insertEmits($routeId);
        $this->insertHandles();
        
        Records::from('stat_event_counts')->insert([
            'route_id' => $routeId,
            'subscriber_count' => $subscribers->count(),
            'emit_count' => count($this->emitCollector->getEmits()),
            'handle_count' => $this->handleCount
        ]);
    }

    private function insertSubscribers(int $routeId)
    {
        $subscriberStats = $this->subscriberCollector->getSubscriberStats();
        $subscriberStats->rewind();
        while ($subscriberStats->valid()) {
            /** @var callable $macro */
            $macro = $subscriberStats->key();
            /** @var EventSubscriberStat $stat */
            $stat = $subscriberStats->getInfo();
            $stat->route_id = $routeId;
            $stat->insert();
            $subscriberStats->next();
        }
    }

    private function insertEmits(int $routeId)
    {
        $emits = $this->emitCollector->getEmits();
        foreach ($emits as $stat) {
            /** @var EventEmitStat $stat */
            $stat->route_id = $routeId;
            $stat->insert();
        }
    }

    private function insertHandles()
    {
        $handles = $this->startHandleCollector->getHandles();
        $subscribers = $this->subscriberCollector->getSubscriberStats();
        $emits = $this->emitCollector->getEmits();
        foreach ($handles as $innerEmitId => $emitHandles) {
            if (   !isset($emits[$innerEmitId]) 
                || $emits[$innerEmitId]->getId() === null) {
                // Некоторые события сознательно не были учтены но могут тут
                // появиться, поэтому, просто проигнорируем их (например события
                // о запросах в БД на вставку данных о самом сборе статистики).
                continue;
            }
            for ($i = 0, $c = count($emitHandles); $i < $c; ++$i) {
                Records::from('stat_event_emit_handles')->insert([
                    'emit_id' => $emits[$innerEmitId]->id,
                    'subscriber_id' => $subscribers[$emitHandles[$i][0]]->id,
                    'duration_sec' => $emitHandles[$i][1]
                ]);
                $this->handleCount += 1;
            }
        }
    }
}