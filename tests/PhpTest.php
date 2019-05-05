<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\{Error};
use tests\engine\CallbackProvider;

class PhpTest extends TestCase
{
    public function testObjectMethodAsCallbackIsError()
    {
        $this->expectException(Error::class);
        $provider = new CallbackProvider;
        $callback = $provider->sumCallback;
    }

    public function testObjectAsCallback()
    {
        $provider = new CallbackProvider;
        $this->assertEquals(3, $provider(1, 2));
    }

    public function testArrayMap()
    {
        $initialArray = ['a' => 1, 'b' => 2];
        
        $mappedArray = array_map(function($element) {
            return $element * 2;
        }, $initialArray);

        $this->assertEquals(['a' => 2, 'b' => 4], $mappedArray);
    }

    public function testIssetArrayKeyDoesNotErrorIfTheArrayIsNull()
    {
        $array = null;
        $this->assertFalse(isset($array['key']));
    }

    // public function testMagicGetParamCanNotBeAnArray()
    // {
    //     $obj = new class {
    //         public function __get($param) {
    //             return is_array($param);
    //         }
    //     };

    //     $this->assertFalse($obj->{['a' => 1, 2]});
    // }
}