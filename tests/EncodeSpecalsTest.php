<?php

use PHPUnit\Framework\TestCase;
use function lightlib\encode_specials;

class EncodeSpecialsTest extends TestCase
{
    public function testEncodeString() 
    {
        $data = '<a>"The \'text\'"</a>';
        $expected = '&lt;a&gt;&quot;The &#39;text&#39;&quot;&lt;/a&gt;';

        $this->assertEquals($expected, encode_specials($data));
    }

    public function testEncodeDeepArrayOfStrings()
    {
        $string = '<a>"The \'text\'"</a>';
        $expectedString = '&lt;a&gt;&quot;The &#39;text&#39;&quot;&lt;/a&gt;';

        $array = [
            'a' => $string,
            'b' => [$string, $string]
        ];

        $expectedArray = [
            'a' => $expectedString,
            'b' => [$expectedString, $expectedString]
        ];

        $this->assertEquals($expectedArray, encode_specials($array));
    }
}