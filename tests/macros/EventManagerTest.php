<?php

use PHPUnit\Framework\TestCase;
use frame\macros\Events;
use tests\macros\examples\MacroEmptyExample;
use tests\macros\examples\MacroAccumulationExample;

class EventsTest extends TestCase
{
    public function testSubscribesAMacroForAnEvent()
    {
        $events = new Events;
        $macro = new MacroEmptyExample;
        $macro2 = new MacroEmptyExample;
        $events->on('event1', $macro);
        $events->on('event1', $macro2);
        $events->on('event3', $macro);

        $this->assertEquals([
            'event1' => [$macro, $macro2],
            'event3' => [$macro]
        ], $events->getSubscribers());
    }

    public function testMacrosHandlesEmits()
    {
        $events = new Events;
        $macro = new MacroAccumulationExample('hello');
        $events->on('say-hello', $macro);

        $this->assertEquals(0, $macro->getCount());
        
        $count = 12;
        for ($i = 0; $i < $count; ++$i) $events->emit('say-hello');

        $this->assertEquals($count, $macro->getCount());
    }
}