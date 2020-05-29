<?php
use PHPUnit\Framework\TestCase;
use function lightlib\array_map_assoc;

class ArrayMapAssocTest extends TestCase
{
    public function testMapArrayWithCallbackHavingKeyAndValueArgsRespectively()
    {
        $array = ['a' => '1', 'b' => '2'];
        $expected = ['a1', 'b2'];
        
        $map = array_map_assoc(function($key, $value) {
            return $key.$value;
        }, $array);

        $this->assertEquals($expected, $map);
    }
}