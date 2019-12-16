<?php namespace frame\macros;

class EventManager
{
    private $subscribers = [];
    private $emits = [];

    public function subscribe(string $event, Macro $macro)
    {
        $this->subscribers[$event][] = $macro;
    }

    public function emit(string $event, ...$args)
    {
        $this->emits[$event] = $this->getEmitCount($event) + 1;
        for ($i = 0, $c = count($this->subscribers[$event] ?? []); $i < $c; ++$i) {
            $this->subscribers[$event][$i]->exec(...$args);
        }
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