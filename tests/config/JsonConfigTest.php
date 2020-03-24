<?php

use PHPUnit\Framework\TestCase;
use frame\stdlib\configs\JsonConfig;

class JsonConfigTest extends TestCase
{
    private static $file = ROOT_DIR.'/tests/config/examples/json';
    private $filedata = [
        'a' => 1,
        'b' => [
            'c' => 2
        ]
    ];

    public static function setUpBeforeClass(): void
    {
        $handle = fopen(self::$file . '.json', 'w');
        $data = [
            'a' => 1,
            'b' => [
                'c' => 2
            ]
        ];
        fwrite($handle, json_encode($data));
        fclose($handle);
    }

    public static function tearDownAfterClass(): void
    {
        unlink(self::$file . '.json');
    }

    public function testLoadsDataFromFile()
    {
        $json = new JsonConfig(self::$file);

        $this->assertEquals($this->filedata['a'], $json->a);
        $this->assertEquals($this->filedata['a']['b'], $json->a['b']);
    }

    public function testTemporaryChangeDataWithoutSaving()
    {
        $json = new JsonConfig(self::$file);
        $json->a = 42;

        // Temporary changed.
        $this->assertEquals(42, $json->a);

        // In the file the value was not changed.
        $json = new JsonConfig(self::$file);
        $this->assertEquals($this->filedata['a'], $json->a);
    }

    public function testChangeFileDataWithSaving()
    {
        $json = new JsonConfig(self::$file);
        $json->a = 42;
        $json->save();

        $json = new JsonConfig(self::$file);
        $this->assertEquals(42, $json->a);

        // Reset old value.
        $json->a = $this->filedata['a'];
        $json->save();
    }
}