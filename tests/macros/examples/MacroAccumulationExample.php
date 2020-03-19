<?php namespace tests\macros\examples;

use frame\events\Macro;

class MacroAccumulationExample extends Macro
{
    private $count = 0;
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function exec(...$args)
    {
        $this->count += 1;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}