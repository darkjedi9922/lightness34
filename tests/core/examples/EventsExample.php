<?php namespace tests\core\examples;

use frame\macros\Events;

class EventsExample extends Events
{
    public $counter = 0;

    public function emit(string $event, ...$args): array
    {
        $this->counter += 1;
        return parent::emit($event, ...$args);
    }
}