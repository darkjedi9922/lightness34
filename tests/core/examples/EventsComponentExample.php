<?php namespace tests\core\examples;

use frame\macros\EventManager;

class EventsComponentExample extends EventManager
{
    public $counter = 0;

    public function emit(string $event, ...$args): array
    {
        $this->counter += 1;
        return parent::emit($event, ...$args);
    }
}