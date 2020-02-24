<?php namespace engine\statistics\lists\count;

class QueryIntervalCountList extends IntervalCountList
{
    protected function getCountField(): string
    {
        return 'stat_queries.id';
    }

    protected function getTimeField(): string
    {
        return 'stat_query_routes.time';
    }

    protected function getFrom(): string
    {
        return 'stat_queries INNER JOIN stat_query_routes 
            ON stat_queries.route_id = stat_query_routes.id';
    }
}