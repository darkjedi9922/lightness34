<?php namespace engine\users\actions\fields;

use frame\cash\config;
use function lightlib\bytes;

class UserAvatarField extends FileField
{
    public function isValidByteSize(): bool
    {
        $config = config::get('users');
        return !$this->file->hasSizeError(bytes(
            $config->{'avatar.max_size.value'},
            $config->{'avatar.max_size.unit'}
        ));
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