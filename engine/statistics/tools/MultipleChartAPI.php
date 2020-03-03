<?php namespace engine\statistics\tools;

use frame\core\Core;
use frame\tools\Init;
use frame\tools\JsonEncoder;
use engine\statistics\lists\MultipleIntervalDataList;

class MultipleChartAPI
{
    private $class;

    public function __construct(string $multipleIntervalDataListClass)
    {
        $this->class = $multipleIntervalDataListClass;
        Init::require(is_subclass_of($this->class, MultipleIntervalDataList::class));
    }

    public function jsonResult()
    {
        $router = Core::$app->router;

        $limit = $router->getArg('limit') ?? 5;
        $sortField = $router->getArg('field') ?? 'max'; // 'max' or 'avg'
        $sortOrder = $router->getArg('order') ?? 'desc'; // 'desc' or 'asc'
        $intervals = $router->getArg('intervals') ?? 10;
        $secInterval = $router->getArg('sec_interval')
            ?? MultipleIntervalDataList::DAY_INTERVAL;

        Init::require($limit > 0);
        Init::require($sortField === 'max' || $sortField === 'avg');
        Init::require($sortOrder === 'desc' || $sortOrder === 'asc');
        Init::require($intervals > 0);
        Init::require($secInterval >= 0);

        $class = $this->class;
        /** @var MultipleIntervalDataList $list */
        $list = new $class($limit, $intervals, $secInterval, $sortField, $sortOrder);
        echo JsonEncoder::forViewText($list->assembleArray());
    }
}