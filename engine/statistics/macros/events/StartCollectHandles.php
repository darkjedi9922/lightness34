<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\TimeStat;

class StartCollectHandles extends BaseStatCollector
{
    /**
     * Array (int) innerEmitId => [
     *  [macro, duration_sec|TimeStat]
     * ]
     */
    private $handles = [];

    /**
     * Array (int) innerEmitId => [
     *  [macro, duration_sec|TimeStat]
     * ]
     */
    public function getHandles(): array
    {
        return $this->handles;
    }

    public function getHandleTimer(int $innerEmitId, callable $macro): ?TimeStat
    {
        foreach ($this->handles[$innerEmitId] as $handle) {
            if ($handle[0] === $macro && ($handle[1] instanceof TimeStat)) {
                return $handle[1];
            }
        }
        return null;
    }
    
    public function setHandleDuration(
        int $innerEmitId,
        callable $macro,
        float $seconds
    ) {
        foreach ($this->handles[$innerEmitId] as &$handle) {
            if ($handle[0] === $macro) {
                $handle[1] = $seconds;
                return;
            }
        }
    }

    protected function collect(...$args)
    {
        $emitId = $args[0];
        $macro = $args[2];

        // Не собираем статистику о статистике.
        if ($macro instanceof BaseStatCollector) return;
        
        // Сначала добавим TimeStat на место времени обработки, чтобы потом взять его
        // в конце обработки и заменить на результат таймера - кол-во прошедших сек.
        $timer = new TimeStat;
        $this->handles[$emitId][] = [$macro, $timer];
        $timer->start();
    }
}