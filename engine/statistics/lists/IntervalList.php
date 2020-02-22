<?php namespace engine\statistics\lists;

use frame\lists\base\BaseList;

abstract class IntervalList implements BaseList
{
    const HOUR_INTERVAL = 60*60;
    const DAY_INTERVAL = self::HOUR_INTERVAL * 24;
    const WEEK_INTERVAL = self::DAY_INTERVAL * 7;
    const MONTH_INTERVAL = self::DAY_INTERVAL * 30;

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
        switch ($this->getSecondInterval()) {
            case self::HOUR_INTERVAL: return date('d.m.Y H', $timestamp) . 'h';
            case self::DAY_INTERVAL: return date('d.m.Y', $timestamp);
            case self::WEEK_INTERVAL: return date('Y/w', $timestamp) . 'w';
            case self::MONTH_INTERVAL: return date('m.Y', $timestamp);
            default: $this->getSecondInterval() . 's';
        }
    }
}