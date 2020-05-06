<?php
use PHPUnit\Framework\TestCase;
use frame\stdlib\drivers\cash\StaticCashStorage;
use frame\core\Core;

class StaticCashStorageTest extends TestCase
{
    public function testCashesWithCallbackAndReturns()
    {
        $app = new Core;
        $cash = StaticCashStorage::getDriver();
        $count = 1;

        $countInitCallable = function() use (&$count) {
            $result = $count;
            $count++;
            return $result;
        };

        $this->assertEquals(1, $count);
        
        $this->assertEquals(1, $cash->cash('answer', $countInitCallable));
        $this->assertEquals(2, $count);
        
        $this->assertEquals(1, $cash->cash('answer', $countInitCallable));
        $this->assertEquals(2, $count);
    }
}