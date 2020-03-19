<?php namespace tests\core\examples;

use frame\events\Events;
use frame\core\Decorator;

class EventsDecoratorExample extends Decorator
{
    public static $counter = 0;
    private $manager;

    public function __construct(Events $manager)
    {
        parent::__construct($manager);
        $this->manager = $manager;
    }

    public function emit(string $event, ...$args): array
    {
        self::$counter += 1;
        return $this->manager->emit($event, ...$args);
    }
}
