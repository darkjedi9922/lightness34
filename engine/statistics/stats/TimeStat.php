<?php namespace engine\statistics\stats;

class TimeStat
{
    private static $timers = [];

    public static function pauseAll()
    {
        for ($i = 0, $c = count(self::$timers); $i < $c; ++$i) 
            self::$timers[$i]->pause();
    }

    public static function resumeAll()
    {
        for ($i = 0, $c = count(self::$timers); $i < $c; ++$i)
            self::$timers[$i]->resume();
    }

    private $start = null;
    private $pause = null;

    public function __construct()
    {
        self::$timers[] = $this;
    }

    public function start()
    {
        if ($this->start !== null) return;
        $this->start = microtime(true);
    }

    public function pause()
    {
        if ($this->start === null || $this->pause !== null) return;
        $this->pause = microtime(true);
    }

    public function resume()
    {
        if ($this->pause === null) return;
        $this->start += microtime(true) - $this->pause;
        $this->pause = null;
    }

    public function resultInSeconds(): float
    {
        if ($this->start === null) return 0;
        $diff = microtime(true) - $this->start;
        $this->start = null;
        $this->pause = null;
        return $diff;
    }
}