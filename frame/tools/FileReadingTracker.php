<?php namespace frame\tools;

use frame\tools\Client;
use frame\tools\transmitters\DatabaseTransmitter;

use function lightlib\count_file_lines;

class FileReadingTracker
{
    private $file;
    private $transmitter;
    private $lineAmount;
    private $userId;

    public function __construct(string $file, int $userId)
    {
        $this->file = $file;
        $this->transmitter = new DatabaseTransmitter;
        $this->lineAmount = count_file_lines($this->file);
        $this->userId = $userId !== 0 ? $userId : Client::getId();
    }

    public function countLines(): int
    {
        return $this->lineAmount;
    }

    public function countOldLines(): int
    {
        $data = $this->file.'_readed_by_'.$this->userId;
        if ($this->transmitter->isSetData($data)) return $this->transmitter->getData($data);
        else return 0;
    }

    public function countNewLines(): int
    {
        $new = $this->countLines() - $this->countOldLines();
        if ($new < 0) return 0;
        return $new;
    }

    public function setReaded()
    {
        $data = $this->file.'_readed_by_'.$this->userId;
        if ($this->countLines() !== 0) $this->transmitter->setData($data, $this->countLines());
        else $this->transmitter->removeData($data);
        $this->transmitter->save();
    }

    public function setUnreadedForAll()
    {
        foreach ($this->transmitter->toArray() as $name => $value) {
            if (strpos($name, $this->file.'_readed_by_') === 0) {
                $this->transmitter->removeData($name);
            }
        }
    }
}