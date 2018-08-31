<?php

use PHPUnit\Framework\TestCase;

/**
 * @testdox lightness.lib
 */
final class LightnessLibTest extends TestCase
{
    /**
     * @test
     * @testdox starts session in the first time
     */
    public function session_start_once_1()
    {
        $this->assertTrue(@session_start_once());
    }

    /**
     * @test
     * @testdox does not start session in the second times
     */
    public function session_start_once_2()
    {
        @session_start_once();
        $this->assertFalse(@session_start_once());
    }

    /**
     * @test
     * @testdox translits strings
     */
    public function translit_1()
    {
        $input = 'Библиотека Lightness';
        $expected = 'Biblioteka Lightness';

        $actual = translit($input);
        $this->assertEquals($expected, $actual);
    }
}