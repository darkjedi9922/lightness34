<?php

use frame\tools\File;
use frame\tools\files\Directory;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    public function testDeletesEmptyDirectory()
    {
        $path = ROOT_DIR . '/tests/tools/examples/new-dir';
        mkdir($path);
        
        $this->assertDirectoryExists($path);
        Directory::delete($path);
        $this->assertDirectoryNotExists($path);
    }

    public function testDeletesNonEmptyDirectory()
    {
        $path = ROOT_DIR . '/tests/tools/examples/new-dir';
        mkdir($path);
        mkdir("$path/new-subdir");
        File::create("$path/new-file.txt");
        File::create("$path/new-subdir/new-subfile.txt");

        Directory::deleteNonEmpty($path);
        $this->assertDirectoryNotExists($path);
    }
}