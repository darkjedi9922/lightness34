<?php namespace engine\statistics\lists\count;

use engine\statistics\lists\TimeIntervalList;
use frame\database\QueryResult;
use frame\stdlib\cash\database;
use Generator;

/**
 * Каждый элемент списка это массив вида ['time' => string, 'count' => int].
 */
abstract class IntervalCountList extends TimeIntervalList
{
    private $limit;
    private $result;

    public function __construct(int $secondsInterval, int $limit)
    {
        $currentInterval = (int)(time() / $secondsInterval) * $secondsInterval 
            + $secondsInterval;
        $minInterval = $currentInterval - $secondsInterval * ($limit + 1);
        parent::__construct($minInterval, $currentInterval, $secondsInterval);
        $this->limit = $limit;
        $this->result = $this->query();
    }

    public function getIterator(): Generator
    {
        $result = array_reverse($this->result->readAll());
        $currentRow = 0;
        $times = parent::getIterator();

        foreach ($times as $interval) {
            $count = 0;
            if (($result[$currentRow]['interval_time'] ?? null) === $interval) {
                $count = $result[$currentRow]["COUNT({$this->getCountField()})"];
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

    protected abstract function getCountField(): string;
    protected abstract function getTimeField(): string;
    protected abstract function getFrom(): string;

    private function query(): QueryResult
    {
        $intervalField = $this->getTimeField();
        $interval = $this->getSecondInterval();

        // Чтобы получить целое время запроса округленное к интервалу, нужно сначала
        // разделить его на время интервала, отбросив получившуюся дробную часть, и
        // умножить на то же время интервала, чтобы заполнить ту часть нулями целой
        // части.
        return database::get()->query(
            "SELECT 
                COUNT({$this->getCountField()}),
                FLOOR($intervalField / $interval) * $interval as interval_time
            FROM {$this->getFrom()}
            GROUP BY interval_time
            ORDER BY interval_time DESC
            LIMIT {$this->limit}"
        );
    }
}