<?php namespace engine\statistics\lists;

use frame\lists\base\BaseList;

abstract class IntervalList implements BaseList
{
    const MINUTE_INTERVAL = 60;
    const HOUR_INTERVAL = 60*60;
    const DAY_INTERVAL = self::HOUR_INTERVAL * 24;
    const MONTH_INTERVAL = self::DAY_INTERVAL * 30;
    const YEAR_INTERVAL = self::MONTH_INTERVAL * 12;

    private $secondInterval;

    public function __construct(int $secondInterval)
    {
        $this->secondInterval = $secondInterval;
    }

    public function getSecondInterval(): int
    {
        return $this->secondInterval;
    }

    public abstract function count();
    public abstract function getIterator();

    public function getIntervalDate(int $timestamp): string
    {
        switch ($this->getIntervalUnit()) {
            case self::MINUTE_INTERVAL: return date('d.m.Y H:i', $timestamp) . 'm';
            case self::HOUR_INTERVAL: return date('d.m.Y H', $timestamp) . 'h';
            case self::DAY_INTERVAL: return date('d.m.Y', $timestamp);
            case self::MONTH_INTERVAL: return date('m.Y', $timestamp);
            case self::YEAR_INTERVAL: return date('Y', $timestamp);
            default: return $this->getSecondInterval() . 's';
        }
    }

    private function getIntervalUnit(): int
    {
        if ($this->secondInterval >= self::YEAR_INTERVAL)
            return self::YEAR_INTERVAL;
        else if ($this->secondInterval >= self::MONTH_INTERVAL)
            return self::MONTH_INTERVAL;
        else if ($this->secondInterval >= self::DAY_INTERVAL)
            return self::DAY_INTERVAL;
        else if ($this->secondInterval >= self::HOUR_INTERVAL)
            return self::HOUR_INTERVAL;
        return self::MINUTE_INTERVAL;
    }
}