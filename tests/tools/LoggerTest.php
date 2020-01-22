<?php

use frame\tools\Logger;
use frame\tools\files\File;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testCreatesLogFileIfItDoesNotExist()
    {
        $file = ROOT_DIR . '/tests/tools/examples/not-existence-dir/log.txt';
        $logger = new Logger($file);
        $this->assertFileExists($file);
        File::delete($file);
    }

    public function testWritesAndReadsRecords()
    {
        $file = ROOT_DIR . '/tests/tools/examples/new-log.txt';
        $logger = new Logger($file);
        
        $messageOne = 'Some message text';
        $messageTwo = "Some message text\nwith several\nlines";

        $logger->write(Logger::TESTING, $messageOne);
        $logger->write(Logger::INFO, $messageTwo);
        
        // Важно проверить именно две записи в одном тесте, чтобы убедиться, что
        // несколько записей правильно разделяются.
        $records = $logger->read();
        $recordOne = $records[0];
        $recordTwo = $records[1];

        $dateRegExp = '/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}$/';

        $this->assertRegExp($dateRegExp, $recordOne['date']);
        $this->assertEquals('CLI', $recordOne['ip']);
        $this->assertEquals(Logger::TESTING, $recordOne['type']);
        $this->assertEquals($messageOne, $recordOne['message']);

        $this->assertRegExp($dateRegExp, $recordTwo['date']);
        $this->assertEquals('CLI', $recordTwo['ip']);
        $this->assertEquals(Logger::INFO, $recordTwo['type']);
        $this->assertEquals($messageTwo, $recordTwo['message']);

        File::delete($file);
    }
}