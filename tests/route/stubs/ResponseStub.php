<?php namespace tests\route\stubs;

use frame\http\route\UrlResponse;
use frame\events\Events;

class ResponseStub extends UrlResponse
{
    public $text;
    private $finish = false;

    public function setText(string $text)
    {
        $this->text = $text;
    }

    public function finish()
    {
        // Если этот метод уже был вызван, дабы не войти в рекурсию не будем
        // повторять все по новой.
        if ($this->finish) return;

        $this->finish = true;
        Events::getDriver()->emit(self::EVENT_FINISH);
    }
}