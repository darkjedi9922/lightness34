<?php namespace tests\actions\examples;

use frame\actions\ActionBody;
use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;

class GetListActionExample extends ActionBody
{
    public function listGet(): array
    {
        return [
            'name' => StringField::class,
            'amount' => IntegerField::class
        ];
    }

    public function succeed(array $post, array $files)
    {
        // Here is nothing to do.
    }

    public function getSuccessRedirect(): ?string
    {
        return null;
    }

    public function getFailRedirect(): ?string
    {
        return null;
    }
}