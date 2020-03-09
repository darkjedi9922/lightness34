<?php

use PHPUnit\Framework\TestCase;
use frame\tools\units\ByteUnit;

class ByteUnitTest extends TestCase
{
    /**
     * @dataProvider valueMaxIntUnitValuesProvider
     */
    public function testDefinesMaxIntegralUnitFromAValue(
        int $value,
        int $expectedValue,
        int $expectedUnit
    ) {
        $time = new ByteUnit($value, ByteUnit::BYTES);
        list($maxValue, $maxUnit) = $time->calcMaxInt();
        $this->assertEquals($expectedValue, $maxValue);
        $this->assertEquals($expectedUnit, $maxUnit);
    }

    public function valueMaxIntUnitValuesProvider(): array
    {
        return [
            [1, 1, ByteUnit::BYTES],
            [1023, 1023, ByteUnit::BYTES],
            [1024, 1, ByteUnit::KB],
            [1025, 1025, ByteUnit::BYTES],
            [1024*1024*2, 2, ByteUnit::MB],
            [1024*1024+1024*512, 1536, ByteUnit::KB],
            [1024*1024*1024, 1, ByteUnit::GB],
            [1024*1024*1024*1024, 1, ByteUnit::TB],
            [1024*1024*1024*1024*1024, 1, ByteUnit::PB]
        ];
    }

    /**
     * @dataProvider convertedValuesProvider
     */
    public function testConvertsValuesFromeOneUnitToAnother(
        float $initialValue, int $initialUnit,
        float $convertedValue, int $convertedUnit
    ) {
        $time = new ByteUnit($initialValue, $initialUnit);
        $actualConversion = $time->convertTo($convertedUnit);
        $this->assertEquals($convertedValue, $actualConversion);
    }

    public function convertedValuesProvider(): array
    {
        return [
            [1024, ByteUnit::BYTES, 1, ByteUnit::KB],
            [512, ByteUnit::KB, 0.5, ByteUnit::MB],
            [1.5, ByteUnit::GB, 1024+512, ByteUnit::MB],
            [2, ByteUnit::TB, 1024*2, ByteUnit::GB]
        ];
    }
}