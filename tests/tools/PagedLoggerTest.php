<?php
use PHPUnit\Framework\TestCase;
use frame\tools\logging\Logger;
use frame\tools\logging\PagedLogger;
use frame\tools\files\File;
use frame\tools\logging\SimpleLogger;

class PagedLoggerTest extends TestCase
{
    private $file = ROOT_DIR . '/tests/tools/examples/test-log';

    public function testWritesToNewFileWhenByteLimitHasPassed()
    {
        $byteLimit = 1;
        $currentFile = "{$this->file}.txt";

        $this->assertFileNotExists($currentFile);
        $logger = new PagedLogger($currentFile, $byteLimit);
        $logger->write(Logger::TESTING, '1');
        $this->assertFileExists($currentFile);
        $this->assertEquals(['1'], $this->getLogMessages($currentFile));
        
        $logLastModificationTime = filemtime($currentFile);
        $oldFile = "{$this->file}.$logLastModificationTime.txt";

        /**
         * PHP кеширует размер файла, поэтому в данном случае кеш нужно очистить.
         * @link https://www.php.net/manual/ru/function.clearstatcache.php
         */
        clearstatcache();

        $this->assertFileNotExists($oldFile);
        $logger->write(Logger::TESTING, '2');
        $this->assertFileExists($oldFile);
        $this->assertFileExists($currentFile);

        $this->assertEquals(['2'], $this->getLogMessages($currentFile));
        $this->assertEquals(['1'], $this->getLogMessages($oldFile));

        File::delete($currentFile);
        File::delete($oldFile);
    }

    /**
     * @return string[]
     */
    private function getLogMessages(string $file)
    {
        $logger = new SimpleLogger($file);
        return array_map(function($item) {
            return $item['message'];
        }, $logger->read());
    }
}