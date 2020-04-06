<?php namespace frame\actions\fields;

use frame\actions\fields\UploadedFile;

class FileField extends \frame\actions\ActionField
{
    public function __construct(UploadedFile $value)
    {
        parent::__construct($value); 
    }

    public function canBeSaved(): bool
    {
        return false;
    }

    public function get(): UploadedFile
    {
        return parent::get();
    }

    public function isEmpty(): bool
    {
        return $this->get()->isEmpty();
    }

    public function isOneOfMimes(array $mimes): bool
    {
        return in_array($this->get()->getMime(), $mimes);
    }
}