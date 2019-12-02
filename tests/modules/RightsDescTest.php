<?php

use PHPUnit\Framework\TestCase;
use tests\stubs\RightsDescStub;

class RightsDescTest extends TestCase
{
    public function testCalculatesMaskFromListOfStringRights()
    {
        $desc = new RightsDescStub;
        $rights = $desc->calcMask(['add', 'make']);
        
        // `add` and `make` is the first and second rights.
        // It is two numbers 1 from the end in the binary view.
        $expected = 0b000011;
        
        $this->assertEquals($expected, $rights);
    }
}