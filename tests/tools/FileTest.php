<?php

use frame\tools\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testGetsTheMimeType()
    {
        $file = new File(ROOT_DIR . '/tests/tools/examples/text.exe');
        $this->assertEquals('text/plain', $file->getMime());
    }

    public function testCreatesANewFile()
    {
        $path = ROOT_DIR . '/tests/tools/examples/new-file.txt';
        File::create($path);
        $this->assertFileExists($path);
        unlink($path);
    }

    public function testDeletesAFile()
    {
        $path = ROOT_DIR . '/tests/tools/examples/to-delete.txt';
        $handle = fopen($path, 'w');
        fclose($handle);
        $this->assertFileExists($path);
        
        File::delete($path);
        $this->assertFileNotExists($path);
    }
}