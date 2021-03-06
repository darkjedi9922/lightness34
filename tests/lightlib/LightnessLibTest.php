<?php

use PHPUnit\Framework\TestCase;
use function lightlib\session_start_once;
use function lightlib\translit;
use function lightlib\substring;
use function lightlib\shorten;
use function lightlib\empty_recursive;
use function lightlib\stored;

/**
 * @testdox lightness.lib
 */
final class LightnessLibTest extends TestCase
{
    /**
     * @test
     * @testdox starts session in the first time
     * @runInSeparateProcess
     */
    public function session_start_once_1()
    {
        $this->assertTrue(@session_start_once());
    }

    /**
     * @test
     * @testdox does not start session in the second times
     * @runInSeparateProcess
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

    /**
     * @dataProvider substringProvider
     */
    public function testReturnsSubstringsByStartIndexAndLength($str, $expected, $start, $length = null)
    {
        $actual = substring($str, $start, $length);
        $this->assertEquals($expected, $actual);
    }

    /**
     * 0: the string
     * 1: substring
     * 2: start index for substring
     * 3: length for substring (may be null)
     */
    public function substringProvider()
    {
        return [
            ['This is Lightness!', 'Light', 8, 5],
            ['Это фреймворк', 'Это', 0, 3],
            ['Выбирай Lightness', 'Lightness', 8],
            ['12345', '45', -2],
            ['12345', '4', -2, 1]
        ];
    }

    /**
     * @dataProvider stringsToShortenProvider
     */
    public function testShortensStrings($str, $expected, $length, $ending = '')
    {
        $actual = shorten($str, $length, $ending);
        $this->assertEquals($expected, $actual);
    }

    /**
     * 0: the string
     * 1: the shorten string
     * 2: length of the shorten string
     * 3: ending or null
     */
    public function stringsToShortenProvider()
    {
        return [
            ['String', 'Str', 3],
            ['Фреймворк', 'Фрейм...', 5, '...'],
            ['42 - ответ', '42 ', 3],
            ['mini', 'mini', 10],
            ['mini', '', 0],
            ['', '', 1, '...'],
            ['', '', 0, '...']
        ];
    }

    public function testEmptyRecursive()
    {
        $deepEmpty = [[], [], 'a' => [
            'b' => [
                false,
                null,
                '',
                []
            ]
        ]];

        $notEmpty = [[], [], 'a' => [
            'b' => [
                null,
                '',
                [],
                true // not entirely empty
            ]
        ]];

        $this->assertTrue(empty_recursive($deepEmpty));
        $this->assertFalse(empty_recursive($notEmpty));
    }

    public function testStored()
    {
        $storage = [
            'size' => 'px'
        ];

        $this->assertEquals(42, stored($storage, 'answer', function() {
            return 42;
        }));

        $this->assertEquals('px', stored($storage, 'size', function() {
            throw new Exception('This exception must not be raised');
        }));
    }
}