<?php

use PHPUnit\Framework\TestCase;
use tests\macros\examples\HookableExample;
use tests\macros\examples\MacroAccumulationExample;

class HookableTest extends TestCase
{
    public function testExecutesMacrosOnAHook()
    {
        HookableExample::addHook('some-hook', new MacroAccumulationExample('hello'));
        HookableExample::addHook('some-hook', new MacroAccumulationExample('macros'));
        HookableExample::addHook('other-hook', new MacroAccumulationExample('world'));

        $hookable = new HookableExample;
        $hookable->doSomethingWithHooks('some-hook');

        $accumulatedMessages = MacroAccumulationExample::getAccumulatedMessages();
        $this->assertEquals(['hello', 'macros'], $accumulatedMessages);
    }
}