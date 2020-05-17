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
        $baseFile = "{$this->file}.txt";
        $firstFile = "{$this->file}.1.txt";

        $this->assertFileNotExists($firstFile);
        $logger = new PagedLogger($baseFile, $byteLimit);
        $logger->write(Logger::TESTING, '1');
        $this->assertFileExists($firstFile);
        $this->assertEquals(['1'], $this->getLogMessages($firstFile));
        
        $newFile = "{$this->file}.2.txt";

        /**
         * PHP кеширует размер файла, поэтому в данном случае кеш нужно очистить.
         * @link https://www.php.net/manual/ru/function.clearstatcache.php
         */
        clearstatcache();

        $this->assertFileNotExists($newFile);
        $logger->write(Logger::TESTING, '2');
        $this->assertFileExists($newFile);
        $this->assertFileExists($firstFile);

        $this->assertEquals(['2'], $this->getLogMessages($newFile));
        $this->assertEquals(['1'], $this->getLogMessages($firstFile));

        File::delete($firstFile);
        File::delete($newFile);
        File::delete(ROOT_DIR . '/runtime/current_log');
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