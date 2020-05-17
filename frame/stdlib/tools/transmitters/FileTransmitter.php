<?php namespace frame\stdlib\tools\transmitters;

use frame\tools\DataTransmitter;
use Exception;
use frame\tools\files\File;

class FileTransmitter extends DataTransmitter
{
    private $file;
    private $data = [];
    private $modified = false;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->reload();
    }

    public function __destruct()
    {
        if ($this->modified) $this->save();
    }

    public function setData($name, $value)
    {
        $this->data[$name] = $value;
        $this->modified = true;
    }

    public function getData($name)
    {
        if (!$this->isSetData($name))
            throw new Exception("Value \"$name\" does not set in the transmitter");
        return $this->data[$name];
    }

    public function isSetData($name)
    {
        return array_key_exists($name, $this->data);
    }

    public function removeData($name)
    {
        unset($this->data[$name]);
    }

    public function toArray()
    {
        return $this->data;
    }

    public function reload()
    {
        if (file_exists($this->file)) {
            $contents = file_get_contents($this->file);
            if ($contents === false) throw new Exception(
                "Error during getting contents of file \"{$this->file}\"");
            $this->data = unserialize($contents);
        };
        $this->modified = false;
    }

    public function save()
    {
        File::createFullPath($this->file, false);
        if (file_put_contents($this->file, serialize($this->data)) === false)
            throw new Exception(
                "Error during putting contents" .
                " to a file \"{$this->file}\""
            );
        $this->modified = false;
    }
}