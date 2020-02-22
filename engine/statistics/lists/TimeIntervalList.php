<?php namespace engine\statistics\lists;

use Generator;

/**
 * Временные интервалы между левой и правой границами, не включая их.
 */
class TimeIntervalList extends IntervalList
{
    private $leftTimestamp;
    private $rightTimestamp;

    public function __construct(
        int $leftTimestamp,
        int $rightTimestamp,
        int $secondsInterval
    ) {
        parent::__construct($secondsInterval);
        $this->leftTimestamp = $leftTimestamp;
        $this->rightTimestamp = $rightTimestamp;
    }

    public function getLeftBorder(): int
    {
        return $this->leftTimestamp;
    }

    public function getRightBorder(): int
    {
        return $this->rightTimestamp;
    }

    public function count(): int
    {
        $timeDiff = abs($this->leftTimestamp - $this->rightTimestamp);
        return ($timeDiff - $this->getSecondInterval()) / $this->getSecondInterval();
    }

    public function getIterator()
    {
        $times = $this->count();
        $interval = $this->getSecondInterval();
        if (!$times) return; 
        // Если идем от раннего времени к позднему, направление будет 1, иначе -1.
        $direction = ($this->rightTimestamp - $this->leftTimestamp) > 0 ? 1 : -1;
        $prevTime = $this->leftTimestamp;
        for ($i = 0; $i < $times; ++$i) {
            $nextTime = $prevTime + $interval * $direction * ($i + 1);
            yield $nextTime;
        }
    }
}