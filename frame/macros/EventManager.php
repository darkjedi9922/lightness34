<?php namespace frame\macros;

class EventManager
{
    private $subscribers = [];
    private $emits = [];
    private $handleStack = [];

    public function subscribe(string $event, callable $macro)
    {
        $this->subscribers[$event][] = $macro;
    }

    /**
     * Возвращает массив callable макросов, которые были вызваны. 
     */
    public function emit(string $event, ...$args): array
    {
        $this->emits[$event] = $this->getEmitCount($event) + 1;
        $subscribers = $this->subscribers[$event] ?? [];
        if (empty($subscribers)) return [];
        
        $result = [$event, $args, []];
        $this->handleStack[] = &$result;
        for ($i = 0, $c = count($subscribers); $i < $c; ++$i) {
            $macro = $this->subscribers[$event][$i];
            $result[2][] = $macro;
            $macro(...$args);
        }
        return array_pop($this->handleStack)[2];
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
     * 
     * Последнему элементу соответствует последнее на данный момент выполняющееся
     * событие которое содержит массив обработчиков, которые были выполнены вплоть
     * до последнего (что выполняется в текущий момент).
     */
    public function getHandleStack(): array
    {
        return $this->handleStack;
    }
}