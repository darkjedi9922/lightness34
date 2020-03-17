<?php namespace frame\macros;

use frame\core\Driver;

/**
 * Реализует механизм событий.
 */
class Events extends Driver
{
    private $subscribers = [];

    /**
     * Устанавливает обработчик на любое событие, которое вызывается через emit().
     * События могут устаналиваться любые в пределах всего менеджера событий.
     * Важно лишь, чтобы они не совпали по имени.
     */
    public function on(string $event, callable $macro)
    {
        $this->subscribers[$event][] = $macro;
    }

    /**
     * Убирает установленный ранее обработчик события.
     */
    public function off(string $event, callable $macro)
    {
        $index = array_search($macro, $this->subscribers[$event] ?? []);
        if ($index === false) return;
        unset($this->subscribers[$event][$index]);
    }

    /**
     * Вызывает сигнал о произошедшем событии. События могут вызываться любые в 
     * пределах одного менеджера событий. Важно лишь, чтобы они не совпали по имени.
     * 
     * Возвращает массив callable макросов, которые были вызваны. 
     */
    public function emit(string $event, ...$args): array
    {
        $result = [];
        $subscribers = $this->subscribers[$event] ?? [];
        if (!empty($subscribers)) {
            for ($i = 0, $c = count($subscribers); $i < $c; ++$i) {
                $macro = $subscribers[$i];
                $result[] = $macro;
                $macro(...$args);
            }
        }

        return $result;
    }

    public function getSubscribers(): array
    {
        return $this->subscribers;
    }
}