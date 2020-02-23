<?php namespace engine\statistics\lists;

use ArrayIterator;

abstract class MultipleIntervalDataList extends TimeIntervalList
{
    const SORT_FIELD_MAX = 'max';
    const SORT_FIELD_AVG = 'avg';
    const SORT_ORDER_DESC = 'desc';
    const SORT_ORDER_ASC = 'asc';

    private $objectsLimit;
    private $sortField;
    private $sortOrder;

    public function __construct(
        int $objectsLimit,
        int $intervalCount,
        int $secondInterval,
        string $sortField,
        string $sortOrder
    ) {
        $currentInterval = (int)(time() / $secondInterval) * $secondInterval;
        $minInterval = $currentInterval - $secondInterval * ($intervalCount - 1);
        parent::__construct($minInterval, $currentInterval, $secondInterval);
        $this->objectsLimit = $objectsLimit;
        $this->sortField = $sortField;
        $this->sortOrder = $sortOrder;
    }

    public function getObjectsLimit(): int
    {
        return $this->objectsLimit;
    }

    public function getSortField(): string
    {
        return $this->sortField;
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->assembleArray());
    }

    public abstract function assembleArray(): array;
}