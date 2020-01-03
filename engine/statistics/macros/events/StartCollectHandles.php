<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;

class StartCollectHandles extends BaseStatCollector
{
    /**
     * Array (int) innerEmitId => [(array of callables) macros]
     */
    private $handles = [];

    /**
     * Array (int) innerEmitId => (callable) $macro
     */
    public function getHandles(): array
    {
        return $this->handles;
    }

    protected function collect(...$args)
    {
        $emitId = $args[0];
        $macro = $args[2];

        $this->handles[$emitId][] = $macro;
    }
}