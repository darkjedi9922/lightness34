<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventRouteStat;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventEmitStat;
use frame\cash\config;
use frame\cash\database;
use frame\database\Records;

class EndCollectEvents extends BaseStatCollector
{
    private $routeStat;
    private $subscriberCollector;
    private $emitCollector;
    private $startHandleCollector;

    public function __construct(
        EventRouteStat $routeStat,
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
        $routeId = $this->routeStat->insert();
        $this->insertSubscribers($routeId);
        $this->insertEmits($routeId);
        $this->insertHandles();
        $this->deleteOldStats();
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
        foreach ($handles as $innerEmitId => $macros) {
            for ($i = 0, $c = count($macros); $i < $c; ++$i) {
                Records::select('stat_event_emit_handles')->insert([
                    'emit_id' => $emits[$innerEmitId]->id,
                    'subscriber_id' => $subscribers[$macros[$i]]->id
                ]);
            }
        }
    }

    private function deleteOldStats()
    {
        $routeTable = EventRouteStat::getTable();
        $subscribersTable = EventSubscriberStat::getTable();
        $emitsTable = EventEmitStat::getTable();
        $config = config::get('statistics');
        $limit = $config->{'events.history.limit'};
        database::get()->query(
            "DELETE $routeTable, $subscribersTable, $emitsTable, 
                stat_event_emit_handles
            FROM
                $routeTable 
                LEFT OUTER JOIN $subscribersTable 
                    ON $routeTable.id = $subscribersTable.route_id
                LEFT OUTER JOIN $emitsTable
                    ON $routeTable.id = $emitsTable.route_id 
                LEFT OUTER JOIN stat_event_emit_handles
                    ON $emitsTable.id = stat_event_emit_handles.emit_id
                INNER JOIN
                (
                    SELECT id FROM $routeTable 
                    ORDER BY id DESC LIMIT $limit, 999999
                ) AS cond_table
                    ON $routeTable.id = cond_table.id"
        );
    }
}