<?php namespace tests\actions\examples;

use frame\actions\ActionBody;
use frame\actions\fields\FileField;

class FileFieldActionExample extends ActionBody
{
    public $avatarField = null;

    public function listFiles(): array
    {
        return [
            'avatar' => FileField::class
        ];
    }

    public function succeed(array $post, array $files)
    {
        $this->avatarField = $files['avatar'];
    }
}