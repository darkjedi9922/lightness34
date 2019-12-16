<?php namespace tests\macros\examples;

use frame\macros\Macro;

class MacroAccumulationExample implements Macro
{
    private $count = 0;
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function exec()
    {
        $this->count += 1;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}