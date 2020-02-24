<?php namespace engine\statistics\lists\count;

use engine\statistics\stats\RouteStat;

class RouteIntervalCountList extends IntervalCountList
{
    protected function getCountField(): string
    {
        return 'id';
    }

    protected function getTimeField(): string
    {
        return 'time';
    }

    protected function getFrom(): string
    {
        return RouteStat::getTable();
    }
}