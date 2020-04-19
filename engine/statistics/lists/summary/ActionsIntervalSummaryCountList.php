<?php namespace engine\statistics\lists\summary;

use engine\statistics\stats\ActionStat;

class ActionsIntervalSummaryCountList extends IntervalSummaryCountList
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
        return ActionStat::getTable();
    }
}