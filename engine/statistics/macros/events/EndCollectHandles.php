<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\TimeStat;
use frame\Core;

class EndCollectHandles extends BaseStatCollector
{
    private $startCollector;

    public function __construct(StartCollectHandles $startCollector)
    {
        $this->startCollector = $startCollector;
    }

    protected function collect(...$args)
    {
        $emitId = $args[0];
        $event = $args[1];
        $macro = $args[2];

        // Не собираем статистику о статистике.
        if ($macro instanceof BaseStatCollector) return;

        $timer = $this->startCollector->getHandleTimer($emitId, $macro);
        $this->startCollector->setHandleDuration(
            $emitId,
            $macro,
            $timer->resultInSeconds()
        );

        // После конца приложения никаких событий больше не будет. Если где-то во
        // время обработки событий был exit, то мы уже не обработаем конец остальных
        // событий. Но тогда в поле времени обработки события еще не будет результата
        // в секундах. В начале туда был установлен TimeStat, поэтому, если это конец
        // всего, пройдемся по всем этим обработкам и установим конечное время.
        if ($event === Core::EVENT_APP_END) $this->correctFinishHandles();
    }

    private function correctFinishHandles()
    {
        $handles = $this->startCollector->getHandles();
        foreach ($handles as $innerEmitId => $emitHandles) {
            foreach ($emitHandles as $handle) {
                if ($handle[1] instanceof TimeStat) {
                    $this->startCollector->setHandleDuration(
                        $innerEmitId,
                        $handle[0],
                        $handle[1]->resultInSeconds()
                    );
                }
            }
        }
    }
}