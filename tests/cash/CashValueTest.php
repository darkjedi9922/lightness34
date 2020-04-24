<?php

use PHPUnit\Framework\TestCase;
use tests\cash\examples\cash_example;
use frame\core\Core;

class CashValueTest extends TestCase
{
    public function testCreatesOnlyOnce()
    {
        $app = new Core;

        $this->assertEquals(12, cash_example::get(12));
        $this->assertEquals(12, cash_example::get(42));
    }

    public function testIsTiedToCurrentCoreInstance()
    {
        $app = new Core;
        $this->assertEquals(12, cash_example::get(12));

        $newApp = new Core;
        $this->assertEquals(42, cash_example::get(42));
    }
}