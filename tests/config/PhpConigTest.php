<?php

use frame\stdlib\configs\PhpConfig;
use PHPUnit\Framework\TestCase;

class PhpConfigTest extends TestCase
{
    private $existenceFile = ROOT_DIR . '/tests/config/examples/phpconfig';
    private $nonExistenceFile = ROOT_DIR . '/tests/config/examples/neversee';

    public function testChecksExistenceOfTheConfig()
    {
        $this->assertTrue(PhpConfig::exists($this->existenceFile));
        $this->assertFalse(PhpConfig::exists($this->nonExistenceFile));
    }

    public function testLoadsConfig()
    {
        $config = new PhpConfig($this->existenceFile);
        $expectedData = [
            'framework' => 'Lightness',
            'version' => 34
        ];

        $this->assertEquals($expectedData, $config->getData());
    }
}