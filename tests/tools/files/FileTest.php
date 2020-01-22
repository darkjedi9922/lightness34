<?php

use frame\tools\files\File;
use frame\tools\files\Directory;
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

    /**
     * @dataProvider fileToCreateWithFullPathProvider
     */
    public function testCreatesANewFileIncludingFullPath(string $file, ?string $root)
    {
        $file = ROOT_DIR . '/tests/tools/examples/' . $file;
        File::createFullPath($file);
        $this->assertFileExists($file);
        
        unlink($file);
        if ($root !== null)
            Directory::deleteNonEmpty(ROOT_DIR . '/tests/tools/examples/' . $root);
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

    /**
     * Второй элемент массива представляет собой директорию, которая будет удалена.
     */
    public function fileToCreateWithFullPathProvider(): array
    {
        return [[
            'non-existence-dir/new-file.txt',
            'non-existence-dir'
        ], [
            'non-existence-dir/non-existence-subdir/new-file.txt',
            'non-existence-dir'
        ], [
            'new-file.txt',
            null
        ]];
    }
}