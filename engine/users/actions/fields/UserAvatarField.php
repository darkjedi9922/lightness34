<?php namespace engine\users\actions\fields;

use frame\actions\fields\FileField;
use frame\stdlib\cash\config;
use frame\tools\units\ByteUnit;

class UserAvatarField extends FileField
{
    public function isValidByteSize(): bool
    {
        $config = config::get('users');
        return !$this->get()->hasSizeError((int) (new ByteUnit(
            $config->{'avatar.max_size.value'},
            $config->{'avatar.max_size.unit'}
        ))->convertTo(ByteUnit::BYTES));
    }

    public function isImage(): bool
    {
        return $this->isOneOfMimes([
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/gif'
        ]);
    }
}