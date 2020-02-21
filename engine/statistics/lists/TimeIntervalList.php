<?php namespace engine\statistics\lists;

use frame\lists\base\BaseList;
use Generator;

/**
 * Временные интервалы между левой и правой границами, не включая их.
 */
class TimeIntervalList implements BaseList
{
    private $leftTimestamp;
    private $rightTimestamp;
    private $secondsInterval;

    public function __construct(
        int $leftTimestamp,
        int $rightTimestamp,
        int $secondsInterval
    ) {
        $this->leftTimestamp = $leftTimestamp;
        $this->rightTimestamp = $rightTimestamp;
        $this->secondsInterval = $secondsInterval;
    }

    public function count(): int
    {
        $timeDiff = abs($this->leftTimestamp - $this->rightTimestamp);
        return ($timeDiff - $this->secondsInterval) / $this->secondsInterval;
    }

    public function getIterator(): Generator
    {
        $a = date('d.m.Y H', $this->leftTimestamp);
        $b = date('d.m.Y H', $this->rightTimestamp);
        $times = $this->count();
        if (!$times) return; 
        // Если идем от раннего времени к позднему, направление будет 1, иначе -1.
        $direction = ($this->rightTimestamp - $this->leftTimestamp) > 0 ? 1 : -1;
        $prevTime = $this->leftTimestamp;
        for ($i = 0; $i < $times; ++$i) {
            $nextTime = $prevTime + $this->secondsInterval * $direction * ($i + 1);
            yield $nextTime;
        }
    }
}