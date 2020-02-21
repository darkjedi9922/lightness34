<?php

use PHPUnit\Framework\TestCase;
use frame\config\Json;

class JsonTest extends TestCase
{
    private static $file = ROOT_DIR.'/tests/config/json.json';
    private $filedata = [
        'a' => 1,
        'b' => [
            'c' => 2
        ]
    ];

    public static function setUpBeforeClass(): void
    {
        $handle = fopen(self::$file, 'w');
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
        unlink(self::$file);
    }

    public function testNullAsPathToFileIsLikeEmptyFile()
    {
        $json = new Json(null);   
        
        $isset = $json->isset('non-existence-setting-in-non-existence-file');
        
        $this->assertFalse($isset);
    }

    public function testDeepNesting()
    {
        $json = new Json(null);
        $json->k1 = ['k1-1' => ['k1-1-1' => 'v1-1-1']];
        
        $value = $json->k1['k1-1']['k1-1-1'];
        $this->assertEquals('v1-1-1', $value);
    }

    public function testLoadsDataFromFile()
    {
        $json = new Json(self::$file);

        $this->assertEquals($this->filedata['a'], $json->a);
        $this->assertEquals($this->filedata['a']['b'], $json->a['b']);
    }

    public function testTemporaryChangeDataWithoutSaving()
    {
        $json = new Json(self::$file);
        $json->a = 42;

        // Temporary changed.
        $this->assertEquals(42, $json->a);

        // In the file the value was not changed.
        $json = new Json(self::$file);
        $this->assertEquals($this->filedata['a'], $json->a);
    }

    public function testChangeFileDataWithSaving()
    {
        $json = new Json(self::$file);
        $json->a = 42;
        $json->save();

        $json = new Json(self::$file);
        $this->assertEquals(42, $json->a);

        // Reset old value.
        $json->a = $this->filedata['a'];
        $json->save();
    }
}