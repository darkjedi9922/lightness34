<?php namespace frame\route;

use frame\events\Events;

abstract class Response extends \frame\core\Driver
{
    /**
     * Выбрасывается прямо перед полным завершением скрипта, в случае такого запроса.
     * (То есть только тогда, когда кто-то вызвал finish(), а он не вызывается всегда
     * при естественном завершении приложения).
     * 
     * Повторно не выбрасывается. (Во время обработки корректного завершения,
     * например, при вызове finish() это событие не будет снова выброшено, чтобы не
     * войти в рекурсию).
     */
    const EVENT_FINISH = 'response-force-finish';

    private $finish = false;

    public abstract function setUrl(string $url);
    public abstract function getUrl(): ?string;

    public abstract function setText(string $text);

    public abstract function setCode(int $code);
    public abstract function getCode(): int;

    /**
     * Полностью завершает обработку запроса. Предпочительнее, чем exit, потому что
     * этот метод учитывает корректное завершение приложения, в отличии от exit.
     */
    public function finish()
    {
        // Если этот метод уже был вызван, дабы не войти в рекурсию не будем
        // повторять все по новой.
        if ($this->finish) return;
       
        $this->finish = true;
        Events::get()->emit(self::EVENT_FINISH);
        exit;
    }
}