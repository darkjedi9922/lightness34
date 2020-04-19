<?php namespace engine\statistics\lists\summary;

use engine\statistics\stats\RouteStat;

class RoutesIntervalSummaryCountList extends IntervalSummaryCountList
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