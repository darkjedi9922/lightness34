<?php namespace engine\statistics\tools;

use frame\route\InitRoute;
use frame\tools\JsonEncoder;
use engine\statistics\lists\MultipleIntervalDataList;
use frame\stdlib\cash\route;

class MultipleChartAPI
{
    private $class;

    public function __construct(string $multipleIntervalDataListClass)
    {
        $this->class = $multipleIntervalDataListClass;
        InitRoute::require(is_subclass_of($this->class, MultipleIntervalDataList::class));
    }

    public function jsonResult()
    {
        $router = route::get();

        $limit = $router->getArg('limit') ?? 5;
        $sortField = $router->getArg('field') ?? 'max'; // 'max' or 'avg'
        $sortOrder = $router->getArg('order') ?? 'desc'; // 'desc' or 'asc'
        $intervals = $router->getArg('intervals') ?? 10;
        $secInterval = $router->getArg('sec_interval')
            ?? MultipleIntervalDataList::DAY_INTERVAL;

        InitRoute::require($limit > 0);
        InitRoute::require($sortField === 'max' || $sortField === 'avg');
        InitRoute::require($sortOrder === 'desc' || $sortOrder === 'asc');
        InitRoute::require($intervals > 0);
        InitRoute::require($secInterval >= 0);

        $class = $this->class;
        /** @var MultipleIntervalDataList $list */
        $list = new $class($limit, $intervals, $secInterval, $sortField, $sortOrder);
        echo JsonEncoder::forViewText($list->assembleArray());
    }
}