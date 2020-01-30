<?php namespace engine\users\actions\fields;

use frame\actions\UploadedFile;

class FileField
{
    protected $file;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function toFile(): UploadedFile
    {
        return $this->file;
    }

    public function isEmpty(): bool
    {
        return $this->file->isEmpty();
    }

    public function isOneOfMimes(array $mimes): bool
    {
        return in_array($this->file->getMime(), $mimes);
    }
}