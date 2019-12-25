<?php namespace frame\macros;

class EventManager
{
    private $subscribers = [];
    private $emits = [];

    public function subscribe(string $event, callable $macro)
    {
        $this->subscribers[$event][] = $macro;
    }

    /**
     * Возвращает массив callable макросов, которые были вызваны. 
     */
    public function emit(string $event, ...$args): array
    {
        $result = [];
        $this->emits[$event] = $this->getEmitCount($event) + 1;
        for ($i = 0, $c = count($this->subscribers[$event] ?? []); $i < $c; ++$i) {
            $macro = $this->subscribers[$event][$i];
            $macro(...$args);
            $result[] = $macro;
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
}