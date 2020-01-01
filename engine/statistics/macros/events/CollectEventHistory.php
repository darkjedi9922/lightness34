<?php namespace engine\statistics\macros\events;

use frame\Core;
use frame\database\Records;
use engine\statistics\stats\EventRouteStat;
use engine\statistics\stats\EventSubscriberStat;
use engine\statistics\stats\EventEmitStat;
use engine\statistics\macros\BaseStatCollector;

use frame\cash\config;
use frame\cash\database;
use frame\macros\EventManager;

class CollectEventHistory extends BaseStatCollector
{
    /** @var EventRouteStat */
    private $routeStat;
    private $subscribers = [];

    protected function collect(...$args)
    {
        $this->routeStat = new EventRouteStat;
        $history = Core::$app->getEventManager()->getEmitHistory();

        $this->collectRouteStat();
        $this->collectSubscribers($history);
        $this->collectEmits($history);
        $this->deleteOldStats();
    }

    private function collectRouteStat()
    {
        $router = Core::$app->router;
        $this->routeStat->route = $router->pagename;
        $this->routeStat->time = time();
        $this->routeStat->insert();
    }

    private function collectSubscribers(array $history)
    {
        foreach ($history as $point) {
            if ($point[0] !== EventManager::HISTORY_SUBSCRIBE_TYPE) continue;
            $class = str_replace('\\', '\\\\', get_class($point[2]));
            $subscriberStat = new EventSubscriberStat;
            $subscriberStat->route_id = $this->routeStat->id;
            $subscriberStat->event = $point[1];
            $subscriberStat->class = $class;
            $subscriberStat->insert();
            $this->subscribers[] = [$subscriberStat, $point[2]];
        }
    }

    private function collectEmits(array $history)
    {
        foreach ($history as $point) {
            if ($point[0] !== EventManager::HISTORY_EMIT_TYPE) continue;

            $event = $point[1];
            $params = $point[2];
            $handledMacros = $point[3];

            $emitStat = new EventEmitStat;
            $emitStat->route_id = $this->routeStat->id;
            $emitStat->event = $event;
            $emitStat->args_json = json_encode(
                $this->prepareArgs($params),
                JSON_HEX_AMP
            );

            $emitStat->insert();
            $this->insertHandles($emitStat, $handledMacros);
        }
    }

    private function prepareArgs(array $params): array
    {
        $result = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->prepareArgs($value);
            } else if (is_object($value)) {
                $result[$key] = get_class($value) . ' object';
            } else $result[$key] = $value;
        }
        return $result;
    }

    private function insertHandles(EventEmitStat $emit, array $handledMacros)
    {
        foreach ($handledMacros as $macro) {
            Records::select('stat_event_emit_handles')->insert([
                'emit_id' => $emit->id,
                // В данный момент все подписчики, что объявлялись на странице уже
                // должны быть записаны в БД -> у них уже есть id.
                'subscriber_id' => $this->findSubscriberStat($macro)->id
            ]);
        }
    }

    public function findSubscriberStat(callable $macro): ?EventSubscriberStat
    {
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber[1] === $macro) return $subscriber[0];
        }
        return null;
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