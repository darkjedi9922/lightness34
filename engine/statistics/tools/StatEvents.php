<?php namespace engine\statistics\tools;

use frame\core\Decorator;
use frame\macros\Events;

class StatEvents extends Decorator
{
    /**
     * Происходит при подписке нового слушателя на событие.
     * Аргументы: string событие и callable макрос.
     */
    const EVENT_SUBSCRIBE = 'event-subscribe';

    /** 
     * Происходит при отписке слушателя от события.
     * Аргументы: string событие и callable макрос.
     */
    const EVENT_UNSUBSCRIBE = 'event-unsubscribe';

    /**
     * Происходит при сигнале о любом неблокирующем событии.
     * Аргументы: int идентификатор события, string событие и массив его аргументов.
     */
    const EVENT_EMIT = 'event-emit';

    /**
     * Происходит прямо перед запуском макроса.
     * Аргументы: int идентификатор события (который обрабатывает макрос), string
     * событие, callable макрос и массив аргументов события.
     */
    const EVENT_MACRO_START = 'macro-start';

    /**
     * Происходит сразу после работы макроса.
     * Аргументы: int идентификатор события, который обрабатывает макрос, string
     * событие, callable макрос и массив аргументов события.
     */
    const EVENT_MACRO_END = 'macro-end';

    /** @var Events $manager */
    private $manager;
    private $lastEmitId = 0;

    public function __construct($manager)
    {
        parent::__construct($manager);
        $this->manager = $manager;
    }

    public function on(string $event, callable $macro)
    {
        $this->manager->on($event, $macro);
        $this->manager->emit(self::EVENT_SUBSCRIBE, $event, $macro);
    }

    public function off(string $event, callable $macro)
    {
        $this->manager->off($event, $macro);
        $this->manager->emit(self::EVENT_UNSUBSCRIBE, $event, $macro);
    }

    public function emit(string $event, ...$args): array
    {
        $emitId = ++$this->lastEmitId;
        $this->manager->emit(self::EVENT_EMIT, $emitId, $event, $args);

        $result = [];
        $subscribers = $this->manager->getSubscribers()[$event] ?? [];
        if (!empty($subscribers)) {
            for ($i = 0, $c = count($subscribers); $i < $c; ++$i) {
                $macro = $subscribers[$i];
                $this->manager->emit(
                    self::EVENT_MACRO_START, $emitId, $event, $macro, $args
                );
                $result[] = $macro;
                $macro(...$args);
                $this->manager->emit(
                    self::EVENT_MACRO_END, $emitId, $event, $macro, $args
                );
            }
        }

        return $result;
    }
}