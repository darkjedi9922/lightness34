<?php namespace engine\statistics\lists;

use frame\database\SqlDriver;
use ArrayIterator;

use function lightlib\last;

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

    public function assembleArray(): array
    {
        $result = [];
        $queryResult = SqlDriver::getDriver()->query($this->getQuery());
        $interval = $this->getSecondInterval();
        while (($line = $queryResult->readLine()) !== null) {
            $url = $line['object'];
            $currentTime = $line['interval_time'];
            $lastTime = null;
            if (!isset($result[$url])) {
                $result[$url] = [];
                // Левая граница не включается в TimeIntervalList,
                // поэтому возьмем границу на один интервал перед ней.
                $lastTime = $this->getLeftBorder() - $interval;
            } else {
                $lastTime = last($result[$url])['timestamp'];
            }

            foreach (
                // Заполняем промежуток до текущего значения нулями по количеству
                // интервальных промежутков между текущим и следующим.
                new TimeIntervalList($lastTime, $currentTime, $interval) 
                as $timestamp
            ) {
                $result[$url][] = [
                    'value' => null,
                    'time' => $this->getIntervalDate($timestamp),
                    'timestamp' => $timestamp
                ];
            }

            // Затем добавляем текущее время.
            $result[$url][] = [
                'value' => $line['value'],
                'time' => $this->getIntervalDate($currentTime),
                'timestamp' => $currentTime
            ];
        }

        // Нужно добавить нулевые интервалы от последнего интервала 
        // до текущего момента.
        foreach ($result as &$data) {
            foreach (
                new TimeIntervalList(
                    last($data)['timestamp'],
                    // Правая гранциа не включается в TimeIntervalList, поэтому берем
                    // границу на интервал после нее.
                    $this->getRightBorder() + $this->getSecondInterval(),
                    $this->getSecondInterval()
                )
                as $timestamp
            ) {
                $data[] = [
                    'value' => null,
                    'time' => $this->getIntervalDate($timestamp),
                    'timestamp' => $timestamp
                ];
            }
        }

        return $result;
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
}