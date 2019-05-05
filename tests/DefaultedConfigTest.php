<?php

use PHPUnit\Framework\TestCase;
use frame\config\DataConfig;
use frame\config\DefaultedConfig;

class DefaultedConfigTest extends TestCase
{
    protected $simpleData =[
        'a' => 1,
        'b' => [
            'c' => 2
        ],
        'e' => [
            'k' => [5],
            'm' => []
        ]
    ];

    protected $simpleDefaultData = [
        'a' => 20,
        'b' => [
            'c' => 10
        ],
        'd' => 15,
        'e' => [
            'k' => [4],
            'm' => [
                'l' => 3
            ]
        ]
    ];

    /** @var DefaultedConfig */
    private $config;

    public function setUp(): void
    {
        $main = new DataConfig($this->simpleData);
        $default = new DataConfig($this->simpleDefaultData);
        $this->config = new DefaultedConfig($main, $default);
    }

    public function testGet()
    {
        $this->assertEquals(1, $this->config->a);
        $this->assertEquals(2, $this->config->get(['b', 'c']));
        $this->assertEquals(15, $this->config->get('d'));
    }

    public function testGetMergedData()
    {
        $expected = [
            'a' => 1,
            'b' => [
                'c' => 2
            ],
            'd' => 15,
            'e' => [
                'k' => [5],
                'm' => [
                    'l' => 3
                ]
            ]
        ];
        $this->assertEquals($expected, $this->config->getData());
    }
}