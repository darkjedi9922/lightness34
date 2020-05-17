<?php namespace frame\tools\logging;

use frame\tools\logging\SimpleLogger;

class PagedLogger implements Logger
{
    private $filename;
    private $currentLogger;
    private $limitByteSize;

    public function __construct(string $filename, int $limitByteSize)
    {
        $this->filename = $filename;
        $this->limitByteSize = $limitByteSize;
        $this->currentLogger = new SimpleLogger($filename);
    }

    /**
     * Пишет в файл лога, проверяя его на перевал лимита. Если лимит был пройден,
     * создаст новый файл лога и запишет запись в него.
     */
    public function write(string $level, string $message)
    {
        if (filesize($this->filename) >= $this->limitByteSize)
            $this->createNewPage();
        $this->currentLogger->write($level, $message);
    }

    /**
     * Считывает записи текущего лога.
     * 
     * {@inheritdoc}
     */
    public function read(): array
    {
        return $this->currentLogger->read();
    }

    private function createNewPage()
    {
        $parts = explode('.', $this->filename);
        $name = implode('.', array_slice($parts, 0, -1));
        $lastModtime = filemtime($this->filename);
        $ext = $parts[count($parts) - 1];
        rename($this->filename, "$name.$lastModtime.$ext");
        $this->currentLogger = new SimpleLogger($this->filename);
    }
}