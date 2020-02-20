<?php namespace engine\statistics\lists;

use frame\lists\base\BaseList;
use frame\database\QueryResult;
use frame\cash\database;
use Iterator;
use ArrayIterator;

/**
 * Каждый элемент списка это массив вида ['time' => string, 'count' => int].
 */
abstract class IntervalCountList implements BaseList
{
    const HOUR_INTERVAL = 60*60;
    const DAY_INTERVAL = self::HOUR_INTERVAL * 24;
    const WEEK_INTERVAL = self::DAY_INTERVAL * 7;
    const MONTH_INTERVAL = self::DAY_INTERVAL * 30;

    private $secondsInterval;
    private $limit;
    private $result;

    public function __construct(int $secondsInterval, int $limit)
    {
        $this->secondsInterval = $secondsInterval;
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
                $timeDiff = $lastTime - $this->secondsInterval - $currentTime;
                $times = $timeDiff / $this->secondsInterval;
                for ($i = 0; $i < $times; ++$i) {
                    if ($resultCount === $this->limit) break;
                    // Помним, что идем с конца, значит время уменьшается.
                    $prevInterval = $lastTime - $this->secondsInterval * ($i + 1);
                    $result[] = [
                        'time' => $this->getIntervalDate($prevInterval),
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
        $interval = $this->secondsInterval;

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

    private function getIntervalDate(int $timestamp): string
    {
        switch ($this->secondsInterval) {
            case self::HOUR_INTERVAL: return date('d.m.Y H', $timestamp) . 'h';
            case self::DAY_INTERVAL: return date('d.m.Y', $timestamp);
            case self::WEEK_INTERVAL: return date('Y/w', $timestamp) . 'w';
            case self::MONTH_INTERVAL: return date('m.Y', $timestamp);
            default: $this->secondsInterval . 's';
        }
    }
}