<?php namespace tests\macros\examples;

use frame\macros\Macro2;

class MacroAccumulationExample implements Macro2
{
    private static $accumulatedMessages = [];

    private $message;

    public static function getAccumulatedMessages(): array
    {
        return self::$accumulatedMessages;
    }

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function exec()
    {
        self::$accumulatedMessages[] = $this->message;
    }
}