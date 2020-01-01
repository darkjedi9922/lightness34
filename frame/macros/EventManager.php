<?php namespace frame\macros;

class EventManager
{
    const HISTORY_SUBSCRIBE_TYPE = 'subscribe';
    const HISTORY_EMIT_TYPE = 'emit';

    private $subscribers = [];
    private $emits = [];
    private $history = [];

    public function subscribe(string $event, callable $macro)
    {
        $this->subscribers[$event][] = $macro;
        $this->history[] = [self::HISTORY_SUBSCRIBE_TYPE, $event, $macro];
    }

    /**
     * Возвращает массив callable макросов, которые были вызваны. 
     */
    public function emit(string $event, ...$args): array
    {
        $result = [];
        $this->history[] = [self::HISTORY_EMIT_TYPE, $event, $args, &$result];

        $this->emits[$event] = $this->getEmitCount($event) + 1;
        $subscribers = $this->subscribers[$event] ?? [];
        if (empty($subscribers)) return [];
        
        for ($i = 0, $c = count($subscribers); $i < $c; ++$i) {
            $macro = $this->subscribers[$event][$i];
            $result[] = $macro;
            $macro(...$args);
        }

        return $result;
    }

    public function getSubscribers(): array
    {
        return $this->subscribers;
    }

    public function getEmitCount(string $event): int
    {
        return $this->emits[$event] ?? 0;
    }

    /**
     * Стек (индексный массив), где каждый элемент это массив вида
     * [(string) event, (array) event_args, (array of callables) ...handled_macro]
     */
    public function getEmitHistory(): array
    {
        return $this->history;
    }
}