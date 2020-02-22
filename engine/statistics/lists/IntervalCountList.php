<?php namespace engine\statistics\lists;

use frame\database\QueryResult;
use frame\cash\database;
use Iterator;
use ArrayIterator;

/**
 * Каждый элемент списка это массив вида ['time' => string, 'count' => int].
 */
abstract class IntervalCountList extends IntervalList
{
    private $limit;
    private $result;

    public function __construct(int $secondsInterval, int $limit)
    {
        parent::__construct($secondsInterval);
        $this->limit = $limit;
        $this->result = $this->query();
    }

    public function count(): int
    {
        return $this->result->count();
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->assembleArray());
    }

    public function assembleArray(): array
    {
        $this->result->seek(0);
        $result = [];

        // Для расчета количества интервальных промежутков между значениями.
        $resultCount = 0;
        $lastTime = 0;

        // Идем по времени с конца в начало.
        while (($line = $this->result->readLine()) !== null) {
            // Заполняем промежуток до следующего значения нулями по количеству
            // интервальных промежутков между текущим и следующим.
            $currentTime = $line['interval_time'];
            if ($lastTime) {
                $times = new TimeIntervalList(
                    $lastTime, $currentTime, $this->getSecondInterval()
                );
                foreach ($times as $time) {
                    if ($resultCount === $this->limit) break;
                    $result[] = [
                        'time' => $this->getIntervalDate($time),
                        'count' => 0
                    ];
                    $resultCount += 1;
                }
            }
            if ($resultCount === $this->limit) break;

            // Затем добавляем текущее время.
            $result[] = [
                'time' => $this->getIntervalDate($currentTime),
                'count' => $line["COUNT({$this->getCountField()})"]
            ];
            $resultCount += 1;
            $lastTime = $currentTime;
        }

        return array_reverse($result);
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