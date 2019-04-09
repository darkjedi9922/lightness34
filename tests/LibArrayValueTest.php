<?php

use PHPUnit\Framework\TestCase;
use function lightlib\array_get_value;
use function lightlib\array_set_value;

class LibArrayValueTest extends TestCase
{
    protected $array = [3, 12, 'a' => 42, 'b' => ['c' => 3]];

    public function testReturnsIndexedValue()
    {
        $value = array_get_value($this->array, 1);
        $this->assertEquals(12, $value);
    }

    public function testReturnsSimpleKeyedValue()
    {
        $value = array_get_value($this->array, 'b');
        $this->assertEquals(['c' => 3], $value);
    }

    public function testThrowsExceptionIfTheSimpleKeyDoesNotExist()
    {
        $this->expectException(Exception::class);
        array_get_value($this->array, 'non-existence-key');
    }

    public function testReturnsArrayNestedKeyedValue()
    {
        $value = array_get_value($this->array, ['b', 'c']);
        $this->assertEquals(3, $value);
    }

    public function testThrowsErrorIfTheNestedKeyDoesNotExist()
    {
        $this->expectException(Exception::class);
        array_get_value($this->array, ['b', 'non-existence']);
    }

    public function testReturnsTheSameArrayIfNestedKeyArrayIsEmpty()
    {
        $value = array_get_value($this->array, []);
        $this->assertEquals($this->array, $value);
    }

    public function testValueIsReturnedAsCopy()
    {
        $value = array_get_value($this->array, ['b', 'c']);
        $value = 'newvalue';

        $this->assertEquals(3, $this->array['b']['c']);
    }

    public function testSetsNewSimpleValue()
    {
        $expected = $this->array;
        $expected[0] = 4;

        $array = array_set_value($this->array, 0, 4);

        $this->assertEquals($expected, $array);
    }

    public function testSetsNewNestedValue()
    {
        $expected = $this->array;
        $expected['b']['c'] = 4;

        $array = array_set_value($this->array, ['b', 'c'], 4);

        $this->assertEquals($expected, $array);
    }

    public function testSetNewValueFromEmptyKeyArraySetEmptyStringKey()
    {
        $expected = $this->array;
        $expected[''] = 'dj';

        $array = array_set_value($this->array, [], 'dj');

        $this->assertEquals($expected, $array);
    }
}