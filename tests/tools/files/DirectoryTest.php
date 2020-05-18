<?php

use frame\tools\files\File;
use frame\tools\files\Directory;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    /**
     * @dataProvider newDirPathsProvider
     */
    public function testCreatesWithRecursivePath($path, $root)
    {
        $path = ROOT_DIR . "/tests/tools/examples/$path";
        $this->assertDirectoryNotExists($path);
        Directory::createRecursive($path);
        $this->assertDirectoryExists($path);
        Directory::deleteNonEmpty(ROOT_DIR . "/tests/tools/examples/$root");
    }

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

    public function testEmptiesDirectory()
    {
        $path = ROOT_DIR . '/tests/tools/examples/new-dir';
        mkdir($path);
        mkdir("$path/new-subdir");
        File::create("$path/new-file.txt");
        File::create("$path/new-subdir/new-subfile.txt");

        Directory::empty($path);
        $this->assertDirectoryNotExists("$path/new-subdir");
        $this->assertFileNotExists("$path/new-file.txt");

        Directory::delete($path);
        $this->assertDirectoryNotExists($path);
    }

    public function newDirPathsProvider()
    {
        return [[
            'new-dir1/new-subdir2/new-dir3',
            'new-dir1'
        ], [
            'new-dir',
            'new-dir'
        ]];
    }
}