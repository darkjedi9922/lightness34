<?php

use PHPUnit\Framework\TestCase;
use frame\macros\EventManager;
use tests\macros\examples\MacroEmptyExample;
use tests\macros\examples\MacroAccumulationExample;

class EventManagerTest extends TestCase
{
    public function testSubscribesAMacroForAnEvent()
    {
        $events = new EventManager;
        $macro = new MacroEmptyExample;
        $macro2 = new MacroEmptyExample;
        $events->subscribe('event1', $macro);
        $events->subscribe('event1', $macro2);
        $events->subscribe('event3', $macro);

        $this->assertEquals([
            'event1' => [$macro, $macro2],
            'event3' => [$macro]
        ], $events->getSubscribers());
    }

    public function testEmitCountEquals0IfThereIsNoEmits()
    {
        $events = new EventManager;
        $this->assertEquals(0, $events->getEmitCount('event1'));
    }

    public function testCountsEmits()
    {
        $events = new EventManager;
        $count = 3;
        for ($i = 0; $i < $count; ++$i) $events->emit('event1');
        $this->assertEquals($count, $events->getEmitCount('event1'));
    }

    public function testMacrosHandlesEmits()
    {
        $events = new EventManager;
        $macro = new MacroAccumulationExample('hello');
        $events->subscribe('say-hello', $macro);

        $this->assertEquals(0, $macro->getCount());
        
        $count = 12;
        for ($i = 0; $i < $count; ++$i) $events->emit('say-hello');

        $this->assertEquals($count, $macro->getCount());
    }
}