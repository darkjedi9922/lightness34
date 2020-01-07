<?php namespace engine\statistics\macros\events;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\EventEmitStat;
use frame\tools\JsonEncoder;

class CollectEventEmits extends BaseStatCollector
{
    /**
     * Массив вида (int) innerEmitId => (EventEmitStat) stat
     */
    private $emits = [];

    /**
     * Массив вида (int) innerEmitId => (EventEmitStat) stat
     */
    public function getEmits(): array
    {
        return $this->emits;
    }

    protected function collect(...$args)
    {
        $emitId = $args[0];
        $event = $args[1];
        $params = $args[2];

        if ($this->isEventAboutStats($params)) return;

        $jsonEncoder = new JsonEncoder;
        $argsJson = $jsonEncoder->toValidJson($this->prepareArgs($params));

        $emitStat = new EventEmitStat;
        $emitStat->id = null;
        $emitStat->event = $event;
        $emitStat->args_json = str_replace('\\', '\\\\', $argsJson);

        $this->emits[$emitId] = $emitStat;
    }

    private function prepareArgs(array $params): array
    {
        $result = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->prepareArgs($value);
            } else if (is_object($value)) {
                $result[$key] = get_class($value) . ' object';
            } else $result[$key] = $value;
        }
        return $result;
    }

    private function isEventAboutStats(array $params): bool
    {
        foreach ($params as $value) {
            if (   is_string($value) 
                && strpos($value, 'stat_') !== false
            ) return true;
        }
        return false;
    }
}