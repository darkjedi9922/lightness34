<?php namespace engine\statistics\lists\summary;

use engine\statistics\lists\TimeIntervalList;
use frame\stdlib\drivers\database\MySqlDriver;
use Generator;

/**
 * Каждый элемент списка это массив вида ['time' => string, 'count' => int].
 */
abstract class IntervalSummaryCountList extends TimeIntervalList
{
    private $result;

    public function __construct(int $secondsInterval, int $limit)
    {
        $currentInterval = (int)(time() / $secondsInterval) * $secondsInterval 
            + $secondsInterval;
        $minInterval = $currentInterval - $secondsInterval * ($limit + 1);
        parent::__construct($minInterval, $currentInterval, $secondsInterval);
        
        $query = $this->getQuery($secondsInterval, $limit);
        $this->result = MySqlDriver::getDriver()->query($query);
    }

    public function getIterator(): Generator
    {
        $result = array_reverse($this->result->readAll());
        $currentRow = 0;
        $times = parent::getIterator();

        foreach ($times as $interval) {
            $count = 0;
            if (($result[$currentRow]['interval_time'] ?? null) === $interval) {
                $count = $result[$currentRow]['value'];
                $currentRow += 1;
            }

            yield [
                'time' => $this->getIntervalDate($interval),
                'value' => $count
            ];
        }
    }

    public function assembleArray(): array
    {
        $result = [];
        foreach ($this->getIterator() as $value) $result[] = $value;
        return $result;
    }

    /**
     * Запрос должен возвращать таблицу с колонками [value|interval_time],
     * где value - собственно, количество того, что считается, а interval_time - его
     * время интервала, которое считается как FLOOR(timestamp / interval) * interval.
     * 
     * В формуле timestamp - это время записи, interval - переданный в аргументах
     * данной функции $secondsInterval.
     * 
     * Вся выборка должна ограничиваться лимитом в $limit записей, быть
     * сгруппированной по колонке interval_time и отсортировано по ней же в DESC
     * порядке.
     */
    protected abstract function getQuery(int $secondsInterval, int $limit): string;
}