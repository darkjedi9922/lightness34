<?php

use PHPUnit\Framework\TestCase;
use tests\engine\CallbackProvider;
use function lightlib\dump;

class PhpTest extends TestCase
{
    /**
     * @expectedException PHPUnit\Framework\Error\Error
     */
    public function testObjectMethodAsCallbackIsError()
    {
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
}