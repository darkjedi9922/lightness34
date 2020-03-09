<?php

use PHPUnit\Framework\TestCase;
use frame\tools\units\ByteUnit;

class ByteUnitTest extends TestCase
{
    /**
     * @dataProvider valueMaxIntUnitValuesProvider
     */
    public function testDefinesMaxIntegralUnitFromABaseValue(
        int $value,
        int $expectedValue,
        string $expectedUnit
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
            [1024*1024*1024*1024*1024, 1, ByteUnit::PB],
            [0, 0, ByteUnit::BYTES]
        ];
    }

    /**
     * @dataProvider convertedValuesProvider
     */
    public function testConvertsValuesFromeOneUnitToAnother(
        float $initialValue, string $initialUnit,
        float $convertedValue, string $convertedUnit
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

    /**
     * @dataProvider convenientFormProvider
     */
    public function testCalcsConvenientForm(
        float $unconvenientValue, string $unconvenientUnit,
        float $convenientValue, string $convenientUnit
    ) {
        $unit = new ByteUnit($unconvenientValue, $unconvenientUnit);
        list($actualValue, $actualUnit) = $unit->calcConvenientForm();
        $this->assertEquals($convenientValue, $actualValue);
        $this->assertEquals($convenientUnit, $actualUnit);
    }

    public function convenientFormProvider(): array
    {
        return [
            [1024, ByteUnit::BYTES, 1, ByteUnit::KB],
            [0.5, ByteUnit::MB, 512, ByteUnit::KB],
            [1024+512, ByteUnit::MB, 1.5, ByteUnit::GB],
            [1024*2, ByteUnit::GB, 2, ByteUnit::TB],
            [1, ByteUnit::KB, 1, ByteUnit::KB],
            [0, ByteUnit::KB, 0, ByteUnit::KB],
            [99983, ByteUnit::BYTES, 97.6, ByteUnit::KB]
        ];
    }
}