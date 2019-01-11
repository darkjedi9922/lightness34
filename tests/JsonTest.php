<?php

use PHPUnit\Framework\TestCase;
use frame\tools\Json;

class JsonTest extends TestCase
{
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
}