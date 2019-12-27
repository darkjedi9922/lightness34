<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventEmitStat;
use engine\statistics\stats\EventRouteStat;
use frame\Core;

class CollectEventEmits extends BaseStatCollector
{
    private $routeStat;
    private $subscriberCollector;
    private $emits = [];

    public function __construct(
        EventRouteStat $routeStat,
        CollectEventSubscribers $subscriberCollector
    ) {
        $this->routeStat = $routeStat;
        $this->subscriberCollector = $subscriberCollector;
    }

    public function getEmits(): array
    {
        return $this->emits;
    }

    protected function collect(...$args)
    {
        $event = $args[0];
        $params = $args[1];
        $handledMacros = $args[2];

        $emitStat = new EventEmitStat;
        $emitStat->event = $event;

        $handledSubscriberStats = [];
        for ($i = 0, $c = count($handledMacros); $i < $c; ++$i) {
            $handledSubscriberStats[] = 
                $this->subscriberCollector->findSubscriberStat($handledMacros[$i]);
        }

        $this->emits[] = [$emitStat, $params, $handledSubscriberStats];

        // Конец сбора статистики о событиях происходит ПОСЛЕ обработки конечного
        // события приложения (а не во время него). Иначе конец наступает прежде, 
        // чем данный сборщик об этом узнает и соберет последнюю информацию.
        if ($event === Core::EVENT_APP_END) {
            $endCollector = new EndCollectEvents(
                $this->routeStat,
                $this->subscriberCollector,
                $this
            );
            $endCollector->exec();
        }
    }
}