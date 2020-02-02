<?php namespace tests\actions\examples;

use frame\actions\ActionBody;
use frame\actions\fields\BooleanField;

class BoolPostListActionExample extends ActionBody
{
    public $checked;

    public function listPost(): array
    {
        return [
            'checked' => BooleanField::class
        ];
    }

    public function succeed(array $post, array $files)
    {
        $this->checked = $post['checked']->get();
    }
}