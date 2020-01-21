<?php namespace engine\statistics\stats;

class TimeStat
{
    private static $timers = [];

    public static function pauseAll()
    {
        $pause = self::getMicrotime();
        for ($i = 0, $c = count(self::$timers); $i < $c; ++$i) {
            /** @var self $timer */
            $timer = self::$timers[$i];
            if ($timer->start === null || $timer->pause !== null) continue;
            $timer->pause = $pause;
        }
    }

    public static function resumeAll()
    {
        $now = self::getMicrotime();
        for ($i = 0, $c = count(self::$timers); $i < $c; ++$i) {
            /** @var self $timer */
            $timer = self::$timers[$i];
            if ($timer->pause === null) continue;
            $timer->start += $now - $timer->pause;
            $timer->pause = null;
        }
    }

    private static function getMicrotime()
    {
        return round(
            extension_loaded('posixrealtime')
                ? posix_clock_gettime(PSXRT_CLK_REALTIME, PSXRT_AS_STRING)
                : microtime(true),
            6
        );
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
        $this->start = self::getMicrotime();
    }

    public function resultInSeconds()
    {
        if ($this->start === null) return 0;
        $current = self::getMicrotime();
        $diff = round($current - $this->start, 6);
        $this->start = null;
        $this->pause = null;
        return $diff;
    }
}