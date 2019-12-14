<?php namespace tests\macros\examples;

use frame\macros\Macro;

class MacroAccumulationExample implements Macro
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