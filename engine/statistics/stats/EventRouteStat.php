<?php namespace engine\statistics\stats;

use frame\cash\database;

class EventRouteStat extends BaseRouteStat
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
            "SELECT $handlesTable.*
            FROM 
                $handlesTable 
                INNER JOIN $emitsTable ON $handlesTable.emit_id = $emitsTable.id
                INNER JOIN $routeTable ON $emitsTable.route_id = $routeTable.id
            WHERE $routeTable.id = {$this->id}
            ORDER BY $handlesTable.id ASC"
        )->readAll();
    }
}