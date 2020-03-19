<?php namespace tests\core\examples;

use frame\events\Events;

class EventsDriverExample extends Events
{
    public $counter = 0;

    public function emit(string $event, ...$args): array
    {
        $this->counter += 1;
        return parent::emit($event, ...$args);
    }
}