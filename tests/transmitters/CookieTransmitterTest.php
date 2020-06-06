<?php

use PHPUnit\Framework\TestCase;
use frame\http\CookieTransmitter;

/** @runTestsInSeparateProcesses */
class CookieTransmitterTest extends TestCase
{
    /**
     * CookieTransmitter должен сохранить значение сразу, а не уже после
     * перезагрузки страницы (как SessionTransmitter).
     */
    public function testImmediateSaving()
    {
        $cookie = new CookieTransmitter;
        $cookie->setData('answer', '42');

        $this->assertEquals('42', $cookie->getData('answer'));
    }
}