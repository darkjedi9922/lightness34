<?php namespace engine\statistics\stats;

use frame\database\Identity;
use frame\cash\database;

class EventRouteStat extends Identity
{
    public static function getTable(): string
    {
        return 'stat_event_routes';
    }

    public function loadHandles(): array
    {
        $routeTable = self::getTable();
        $emitsTable = EventEmitStat::getTable();
        $handlesTable = 'stat_event_emit_handles';
        return database::get()->query(
            "SELECT $handlesTable.emit_id, $handlesTable.subscriber_id
            FROM 
                $handlesTable 
                INNER JOIN $emitsTable ON $handlesTable.emit_id = $emitsTable.id
                INNER JOIN $routeTable ON $emitsTable.route_id = $routeTable.id
            WHERE $routeTable.id = {$this->id}"
        )->readAll();
    }
}