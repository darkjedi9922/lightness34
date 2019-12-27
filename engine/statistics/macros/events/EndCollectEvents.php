<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventRouteStat;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventEmitStat;
use frame\cash\config;
use frame\cash\database;

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
        $this->insertSubscribers($routeId);
        $this->insertEmits($routeId);
        $this->deleteOldStats();
    }

    private function insertSubscribers(int $routeId)
    {
        $subscribers = $this->subscriberCollector->getSubscribers();
        for ($i = 0, $c = count($subscribers); $i < $c; ++$i) {
            /** @var EventSubscriberStat $subscriber */
            $subscriber = $subscribers[$i][0];
            $subscriber->route_id = $routeId;
            $subscriber->insert();
        }
    }

    private function insertEmits(int $routeId)
    {
        $emits = $this->emitCollector->getEmits();
        for ($i = 0, $c = count($emits); $i < $c; ++$i) {
            /** @var EventEmitStat $emitStat */
            $emitStat = $emits[$i][0];
            $emitStat->route_id = $routeId;
            $emitStat->insert();
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
            "DELETE $routeTable, $subscribersTable, $emitsTable
            FROM
            (
                (
                    $routeTable LEFT OUTER JOIN $subscribersTable 
                    ON $routeTable.id = $subscribersTable.route_id
                )
                LEFT OUTER JOIN $emitsTable
                ON $routeTable.id = $emitsTable.route_id 
            )
            INNER JOIN 
            (
                SELECT id FROM $routeTable ORDER BY id DESC LIMIT $limit, 999999
            ) AS cond_table
            ON $routeTable.id = cond_table.id"
        );
    }
}