<?php namespace frame\macros;

/**
 * Реализует механизм событий.
 * 
 * События могут быть блокирующими. Они определяются символом ! в начале. Во время
 * обработки таких событий нельзя вызывать другие события. 
 * 
 * Обычно они используютс в случаях, когда обработка события может привести к
 * рекурсии (например, событие обрабатывается макросом, который внутри вызывает
 * это же событие).
 * 
 * Одними из блокирующих событий являются события самого EventManager.
 */
class EventManager
{
    /**
     * Происходит при подписке нового слушателя на событие.
     * Аргументы: string событие и callable макрос.
     */
    const BLOCK_EVENT_SUBSCRIBE = '!event-subscribe';

    /**
     * Происходит при сигнале о любом неблокирующем событии.
     * Аргументы: int идентификатор события, string событие и массив его аргументов.
     */
    const BLOCK_EVENT_EMIT = '!event-emit';

    /**
     * Происходит прямо перед запуском макроса.
     * Аргументы: int идентификатор события (который обрабатывает макрос), string
     * событие, callable макрос и массив аргументов события.
     */
    const BLOCK_EVENT_MACRO_START = '!macro-start';

    /**
     * Происходит сразу после работы макроса.
     * Аргументы: int идентификатор события, который обрабатывает макрос, string
     * событие, callable макрос и массив аргументов события.
     */
    const BLOCK_EVENT_MACRO_END = '!macro-end';

    private $subscribers = [];
    private $blocked = false;
    private $lastEmitId = 0;

    public function subscribe(string $event, callable $macro)
    {
        $this->subscribers[$event][] = $macro;
        if (!$this->isBlockingEvent($event)) $this->emit(
            self::BLOCK_EVENT_SUBSCRIBE,
            $event,
            $macro
        );
    }

    /**
     * Возвращает массив callable макросов, которые были вызваны. 
     */
    public function emit(string $event, ...$args): array
    {
        if ($this->blocked)
            throw new \Exception('Currently there is handling a blocking event');

        $emitId = ++$this->lastEmitId;
        $this->blocked = $this->isBlockingEvent($event);

        if (!$this->blocked) $this->emit(
            self::BLOCK_EVENT_EMIT,
            $emitId,
            $event,
            $args
        );

        $result = [];
        $subscribers = $this->subscribers[$event] ?? [];
        if (!empty($subscribers)) {
            for ($i = 0, $c = count($subscribers); $i < $c; ++$i) {
                $macro = $this->subscribers[$event][$i];
                if (!$this->blocked) $this->emit(
                    self::BLOCK_EVENT_MACRO_START,
                    $emitId,
                    $event,
                    $macro,
                    $args
                );
                $result[] = $macro;
                if (!$this->blocked) $this->emit(
                    self::BLOCK_EVENT_MACRO_END,
                    $emitId,
                    $event,
                    $macro,
                    $args,
                );
                $macro(...$args);
            }
        }

        $this->blocked = false;
        return $result;
    }

    public function getSubscribers(): array
    {
        return $this->subscribers;
    }

    public function isBlockingEvent(string $event): bool
    {
        return ($event[0] ?? '') === '!';
    }
}