<?php namespace frame\tools\logging;

use frame\tools\logging\SimpleLogger;
use frame\tools\Semaphores;
use frame\stdlib\tools\transmitters\FileTransmitter;

class PagedLogger implements Logger
{
    private $baseFile;
    private $currentId;
    private $currentLogger;
    private $limitByteSize;
    private $idTransmitter;

    public function __construct(string $baseFile, int $limitByteSize)
    {
        $this->baseFile = $baseFile;
        $this->limitByteSize = $limitByteSize;
        $privateTempFile = ROOT_DIR . '/runtime/current_log';
        $this->idTransmitter = new FileTransmitter($privateTempFile);
        $this->currentId = $this->getCurrentPageId();
        $baseFile = $this->insertId($baseFile, $this->currentId);
        $this->currentLogger = new SimpleLogger($baseFile);
    }

    /**
     * Пишет в файл лога, проверяя его на перевал лимита. Если лимит был пройден,
     * создаст новый файл лога и запишет запись в него.
     */
    public function write(string $level, string $message)
    {
        Semaphores::synchronize("paged-logging", true,
            function() use ($level, $message) {
                $this->checkIfPageWasChanged();
                $file = $this->currentLogger->getFile();
                if (filesize($file) >= $this->limitByteSize)
                    $this->createNewPage();
                $this->currentLogger->write($level, $message);
            }
        );
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

    private function checkIfPageWasChanged()
    {
        $currentId = $this->getCurrentPageId();
        if ($currentId !== $this->currentId) {
            $this->currentId = $currentId;
            $baseFile = $this->insertId($this->baseFile, $currentId);
            $this->currentLogger = new SimpleLogger($baseFile);
        }
    }

    private function createNewPage()
    {
        Semaphores::synchronize('work-with-log-current-id', true, function() {
            $this->currentId += 1;
            $file = $this->insertId($this->baseFile, $this->currentId);
            $this->currentLogger = new SimpleLogger($file);
            $this->idTransmitter->setData('id', $this->currentId);
            $this->idTransmitter->save();
        });
    }

    private function insertId(string $baseFile, int $number)
    {
        $parts = explode('.', $baseFile);
        $name = implode('.', array_slice($parts, 0, -1));
        $ext = $parts[count($parts) - 1];
        return "$name.$number.$ext";
    }

    private function getCurrentPageId(): int
    {
        // Процесс не может получить текущий id в getCurrentId(), пока выполняется
        // создание новой страницы или пока создается самый первый id.
        // Потому что в это время может записываться новый id.
        return Semaphores::synchronize('work-with-log-current-id', true, function() {
            $this->idTransmitter->reload();
            if (!$this->idTransmitter->isSetData('id')) {
                $this->idTransmitter->setData('id', '1');
                $this->idTransmitter->save();
            }
            return $this->idTransmitter->getData('id');
        });
    }
}