<?php
use PHPUnit\Framework\TestCase;
use frame\stdlib\tools\transmitters\FileTransmitter;
use frame\tools\files\Directory;

class FileTransmitterTest extends TestCase
{
    public function testCreatesFileInNonExistenceDirectory()
    {
        $dir = ROOT_DIR . '/tests/transmitters/examples/non-existence-dir';
        $file = "$dir/file";
        $transmitter = new FileTransmitter($file);
        $transmitter->setData('test', 'value');
        
        $this->assertDirectoryNotExists($dir);
        $transmitter->save();
        $this->assertFileExists($file);

        Directory::deleteNonEmpty($dir);
    }
}