<?php

use PHPUnit\Framework\TestCase;
use frame\tools\Debug;

class DebugTest extends TestCase
{
    /**
     * @dataProvider toStringAndTypeProvider
     */
    public function testGivesStringAndType($var, string $strRepr, string $type)
    {
        $this->assertEquals([$strRepr, $type], Debug::getStringAndType($var));
    }

    public function toStringAndTypeProvider(): array
    {
        return [
            [new stdClass, 'stdClass', 'object'],
            [null, 'null', 'null'],
            ['false', 'false', 'string'],
            [[1, 2, 3], "[0 => 1, 1 => 2, 2 => 3]", 'array'],
            [[1, 'b' => 2], "[0 => 1, b => 2]", 'array'],
            [function() {}, 'Closure', 'object'],
            [new class {}, 'anonymous', 'object'],
            [0.1234, '0.1234', 'double'],
            [0, '0', 'integer'],
            [true, 'true', 'boolean'],
            [false, 'false', 'boolean']
        ];
    }
}