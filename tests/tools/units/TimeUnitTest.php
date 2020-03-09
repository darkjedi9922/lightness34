<?php

use PHPUnit\Framework\TestCase;
use frame\tools\units\TimeUnit;

class TimeUnitTest extends TestCase
{
    /**
     * @dataProvider secondsMaxIntUnitValuesProvider
     */
    public function testDefinesMaxIntegralUnitFromSeconds(
        int $seconds,
        int $expectedValue,
        int $expectedUnit
    ) {
        $time = new TimeUnit($seconds, TimeUnit::SECONDS);
        list($maxValue, $maxUnit) = $time->calcMaxInt();
        $this->assertEquals($expectedValue, $maxValue);
        $this->assertEquals($expectedUnit, $maxUnit);
    }

    public function secondsMaxIntUnitValuesProvider(): array
    {
        return [
            [1, 1, TimeUnit::SECONDS],
            [59, 59, TimeUnit::SECONDS],
            [60, 1, TimeUnit::MINUTES],
            [61, 61, TimeUnit::SECONDS],
            [60*60*2, 2, TimeUnit::HOURS],
            [60*60+60*30, 90, TimeUnit::MINUTES],
            [60*60*24, 1, TimeUnit::DAYS],
            [60*60*24*30, 1, TimeUnit::MONTHS],
            [60*60*24*30*12, 1, TimeUnit::YEARS]
        ];
    }

    /**
     * @dataProvider hoursMaxIntUnitValuesProvider
     */
    public function testDefinesMaxIntegralUnitFromHours(
        int $hours,
        int $expectedValue,
        int $expectedUnit
    ) {
        $time = new TimeUnit($hours, TimeUnit::HOURS);
        list($maxValue, $maxUnit) = $time->calcMaxInt();
        $this->assertEquals($expectedValue, $maxValue);
        $this->assertEquals($expectedUnit, $maxUnit);
    }

    public function hoursMaxIntUnitValuesProvider(): array
    {
        return [
            [2, 2, TimeUnit::HOURS],
            [24, 1, TimeUnit::DAYS],
            [24*30*2, 2, TimeUnit::MONTHS],
            [24*30*12, 1, TimeUnit::YEARS]
        ];
    }

    public function testDefinedMaxIntegralUnitAmongAllowedUnits()
    {
        $time = new TimeUnit(30*12, TimeUnit::DAYS); // One year (12 months)

        // The year is disallowed, so max int here is months.
        $this->assertEquals([12, TimeUnit::MONTHS], $time->calcMaxInt([
            TimeUnit::HOURS,
            TimeUnit::MONTHS
        ]));
    }

    /**
     * @dataProvider convertedValuesProvider
     */
    public function testConvertsValuesFromeOneUnitToAnother(
        float $initialValue, int $initialUnit,
        float $convertedValue, int $convertedUnit
    ) {
        $time = new TimeUnit($initialValue, $initialUnit);
        $actualConversion = $time->convertTo($convertedUnit);
        $this->assertEquals($convertedValue, $actualConversion);
    }

    public function convertedValuesProvider(): array
    {
        return [
            [60, TimeUnit::SECONDS, 1, TimeUnit::MINUTES],
            [30, TimeUnit::MINUTES, 0.5, TimeUnit::HOURS],
            [1.5, TimeUnit::HOURS, 90, TimeUnit::MINUTES],
            [2, TimeUnit::MINUTES, 120, TimeUnit::SECONDS]
        ];
    }
}