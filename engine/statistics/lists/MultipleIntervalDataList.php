<?php namespace engine\statistics\lists;

use frame\database\SqlDriver;
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
        $currentInterval = (int)(time() / $secondInterval) * $secondInterval + $secondInterval;
        $minInterval = $currentInterval - $secondInterval * ($intervalCount + 1);
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

    public function assembleArray(): array
    {
        $objectTimestampValues = [];
        $queryResult = SqlDriver::getDriver()->query($this->getQuery());
        while (($line = $queryResult->readLine()) !== null) {
            $object = $line['object'];
            $timestamp = $line['interval_time'];
            $value = $line['value'];
            $objectTimestampValues[$object][$timestamp] = $value;
        }

        $result = [];
        foreach (parent::getIterator() as $timestamp) {
            foreach ($objectTimestampValues as $object => $timestampValues) {
                $result[$object][] = [
                    'value' => $timestampValues[$timestamp] ?? $this->getEmptyValue(),
                    'time' => $this->getIntervalDate($timestamp),
                    'timestamp' => $timestamp
                ];
            }
        }

        return $this->removeAllEmptyValueIntervals($result);
    }

    /**
     * Запрос должен возвращать данные с такими колонками:
     * object | value | interval_time
     */
    protected abstract function getQuery(): string;
    
    /** 
     * Значение, которое будет использоваться, если в интервале нет значений для
     * рассматриваемого объекта.
     * @return mixed|null
     */
    protected abstract function getEmptyValue();

    private function removeAllEmptyValueIntervals(array $result): array
    {
        return array_filter($result, function ($timestampValues) {
            return !$this->doIntervalsHaveOnlyEmptyValues($timestampValues);
        });
    }

    private function doIntervalsHaveOnlyEmptyValues(array $timestampValues): bool
    {
        return empty(array_filter($timestampValues, function ($timestampValue) {
            return $timestampValue['value'] !== $this->getEmptyValue();
        }));
    }
}