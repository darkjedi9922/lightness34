<?php namespace tests\macros\examples;

use frame\macros\Hookable;

class HookableExample extends Hookable
{
    public function doSomethingWithHooks(string $hook)
    {
        static::hook($hook);
    }
}